<?php

namespace App\Http\Controllers\Funtion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use HP;

use App\Models\Section5\Ibcbs;
use App\Models\Section5\IbcbsScope;
use App\Models\Section5\IbcbsScopeDetail;
use App\Models\Section5\IbcbsScopeTis;
use App\Models\Section5\IbcbsInspectors;
use App\Models\Section5\IbcbsCertificate;
use App\Models\Section5\IbcbsGazette;

use App\Models\Section5\Labs;
use App\Models\Section5\LabsScope;
use App\Models\Section5\LabsScopeDetail;

use App\Models\Section5\ApplicationLab;

use App\Models\Basic\BranchGroup;
use App\Models\Basic\Branch;

use App\Models\Tis\Standard;

use App\Models\Bsection5\TestItem;
use App\Models\Bsection5\TestItemTools;

use stdClass;

class Section5Controller extends Controller
{

    public function GetBranchData($id_group)
    {
        if ($id_group === 'ALL') {
            $data = Branch::get();
        } else {
            $data = Branch::where('branch_group_id', $id_group)->get();
        }

        return response()->json($data);
    }

    public function welcome_ibcb_list(Request $request)
    {
        return view('section5/ibcb.index-welcome');
    }

    public function welcome_ibcb_by_scope(Request $request)
    {
        return view('section5/ibcb.index-scope-welcome');
    }

    public function ibcb_by_scope(Request $request)
    {
        return view('section5/ibcb.iframe.index-scope');
    }

    public function ibcb_list(Request $request)
    {
        return view('section5/ibcb.iframe.index');
    }

    public function data_ibcb_scope_list(Request $request)
    {

        $filter_search = $request->get('filter_search');
        $filter_status = $request->get('filter_status');
        $filter_tis_id = $request->get('filter_tis_id');
        $filter_branch = $request->get('filter_branch');
        $filter_branch_group = $request->get('filter_branch_group');
        $filter_layout = !empty($request->get('filter_layout')) ? $request->get('filter_layout') : 'app';


        $query = IbcbsScopeTis::query()->with([
            'scope_detail',
            'ibcb_scope',
            'scope_tis_std',
            'ibcb_data'
        ])
            ->leftJoin((new IbcbsScope)->getTable() . ' AS scope', 'scope.id', '=', 'section5_ibcbs_scopes_tis.ibcb_scope_id')
            ->leftJoin((new BranchGroup)->getTable() . ' AS branch_group', 'branch_group.id', '=', 'scope.branch_group_id')
            ->leftJoin((new IbcbsScopeDetail)->getTable() . ' AS scope_detail', 'scope_detail.id', '=', 'section5_ibcbs_scopes_tis.ibcb_scope_detail_id')
            ->leftJoin((new Branch)->getTable() . ' AS branch', 'branch.id', '=', 'scope_detail.branch_id')
            ->leftJoin((new Standard)->getTable() . ' AS standard', 'standard.id', '=', 'section5_ibcbs_scopes_tis.tis_id')
            ->when($filter_search, function ($query, $filter_search) {
                $search_full = str_replace(' ', '', $filter_search);
                if (strpos($search_full, 'CB-') !== false || strpos($search_full, 'IB-') !== false) {
                    return $query->whereHas('ibcb_data', function ($query) use ($search_full) {
                        $query->where('ibcb_code', 'LIKE', "%$search_full%");
                    });
                } else {
                    return $query->where(function ($query2) use ($search_full) {
                        $query2->whereHas('ibcb_data', function ($query) use ($search_full) {
                            $query->Where(DB::raw("REPLACE(taxid,' ','')"), 'LIKE', "%" . $search_full . "%")
                                ->OrWhere(DB::raw("REPLACE(name,' ','')"), 'LIKE', "%" . $search_full . "%")
                                ->OrWhere(DB::raw("REPLACE(ibcb_code,' ','')"), 'LIKE', "%" . $search_full . "%")
                                ->OrWhere(DB::raw("REPLACE(ibcb_name,' ','')"), 'LIKE', "%" . $search_full . "%")
                                ->OrWhere(DB::raw("REPLACE(branch_group.title,' ','')"), 'LIKE', "%" . $search_full . "%")
                                ->OrWhere(DB::raw("REPLACE(branch.title,' ','')"), 'LIKE', "%" . $search_full . "%")
                                ->OrWhere(DB::raw("REPLACE(standard.title,' ','')"), 'LIKE', "%" . $search_full . "%")
                                ->OrWhere(DB::raw("REPLACE(standard.tis_tisno,' ','')"), 'LIKE', "%" . $search_full . "%");
                        });
                    });
                }
            })
            ->when($filter_status, function ($query, $filter_status) {
                if ($filter_status == 1) {
                    $query->whereHas('ibcb_scope', function ($query) {
                        $query->where('end_date', '>=', date('Y-m-d'));
                    })
                        ->Orwhere(function ($query2) {
                            $query2->whereHas('ibcb_scope', function ($query) {
                                $query->where('type', 2)->where('state', 1);
                            });
                        })
                        ->Orwhere(function ($query2) {
                            $query2->whereHas('ibcb_scope', function ($query) {
                                $query->where('type', 2)->where('end_date', '>', date('Y-m-d'));
                            });
                        });
                } else {
                    $query->whereHas('ibcb_scope', function ($query) {
                        $query->where('end_date', '<', date('Y-m-d'));
                    })
                        ->Orwhere(function ($query2) {
                            $query2->whereHas('ibcb_scope', function ($query) {
                                $query->where('type', 2)->where('state', '<>', 1);
                            });
                        })
                        ->Orwhere(function ($query2) {
                            $query2->whereHas('ibcb_scope', function ($query) {
                                $query->where('type', 2)->where('end_date', '<', date('Y-m-d'));
                            });
                        });
                }
            })
            ->when($filter_tis_id, function ($query, $filter_tis_id) {
                $query->where('tis_id', $filter_tis_id);
            })
            ->when($filter_branch_group, function ($query, $filter_branch_group) {
                $query->whereHas('ibcb_scope', function ($query) use ($filter_branch_group) {
                    $query->where('branch_group_id', $filter_branch_group);
                });
            })
            ->when($filter_branch, function ($query, $filter_branch) {
                $query->whereHas('scope_detail', function ($query) use ($filter_branch) {
                    $query->where('branch_id', $filter_branch);
                });
            });

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('ibcb_code', function ($item) {
                return $item->ibcb_code;
            })
            ->addColumn('ibcb_name', function ($item) {
                $html = !empty($item->ibcb_data->ibcb_name) ? $item->ibcb_data->ibcb_name : '-';
                $html .= !empty($item->ibcb_data->name) ? '<div>(' . $item->ibcb_data->name . ')</div>' : '';
                return $html;
            })
            ->addColumn('initial', function ($item) {
                $html = !empty($item->ibcb_data->initial) ? $item->ibcb_data->initial : '-';
                return $html;
            })
            ->addColumn('bs_branch_group', function ($item) {
                return !empty($item->ibcb_scope->bs_branch_group->title) ? $item->ibcb_scope->bs_branch_group->title : ' - ';
            })
            ->addColumn('bs_branch', function ($item) {
                return !empty($item->scope_detail->bs_branch->title) ? $item->scope_detail->bs_branch->title : ' - ';
            })
            ->addColumn('tis_tisno', function ($item) {
                return (!empty($item->scope_tis_std->tis_tisno) ? $item->scope_tis_std->tis_tisno : null) . ' : ' . (!empty($item->scope_tis_std->title) ? $item->scope_tis_std->title : null);
            })
            ->addColumn('start_date', function ($item) {
                return !empty($item->ibcb_data->ibcb_start_date) ? HP::revertDate($item->ibcb_data->ibcb_start_date) : null;
            })
            ->addColumn('end_date', function ($item) {
                if (!empty($item->ibcb_data->ibcb_end_date)) {
                    return !empty($item->ibcb_data->ibcb_end_date) ? HP::revertDate($item->ibcb_data->ibcb_end_date) : '-';
                } else {
                    return !empty($item->ibcb_scope->end_date) ? HP::revertDate($item->ibcb_scope->end_date) : null;
                }
            })
            ->addColumn('state', function ($item) {
                $StateHtml = [1 => '<span class="text-success">Active</span>', 2 => '<span class="text-danger">Not Active</span>'];

                if ($item->type == 1) {
                    return (!empty($item->ibcb_scope->end_date) && $item->ibcb_scope->end_date >= date('Y-m-d')) && array_key_exists($item->ibcb_scope->state, $StateHtml) ? $StateHtml[$item->ibcb_scope->state] : '<span class="text-danger">Not Active</span>';
                } else {
                    if (!empty($item->ibcb_scope->end_date)) {
                        return (!empty($item->ibcb_scope->end_date) && $item->ibcb_scope->end_date >= date('Y-m-d')) && array_key_exists($item->ibcb_scope->state, $StateHtml) ? $StateHtml[$item->ibcb_scope->state] : '<span class="text-danger">Not Active</span>';
                    } else {
                        return array_key_exists($item->ibcb_scope->state, $StateHtml) ? $StateHtml[$item->ibcb_scope->state] : '<span class="text-danger">Not Active</span>';
                    }
                }
            })
            ->addColumn('contact', function ($item) {
                return !empty($item->ibcb_data->ContactDataAdress) ? $item->ibcb_data->ContactDataAdress : null;
            })
            ->addColumn('gazette', function ($item) {
                $attach_file_gazette = !empty($item->ibcb_scope->application_ibcb_board_approve->attach_file_gazette) ? $item->ibcb_scope->application_ibcb_board_approve->attach_file_gazette : null;
                return !empty($attach_file_gazette) ? '<a href="' . (HP::getFileStorage($attach_file_gazette->url)) . '" target="_blank">' . (HP::FileExtension($attach_file_gazette->filename) ?? '') . '</a>' : '-';
            })
            ->addColumn('action', function ($item) use ($filter_layout) {
                if (!empty($item->ibcb_scope->ibcb->ibcb_code)) {
                    return ' <a href="' . url('section5/ibcb/' . base64_encode($item->ibcb_scope->ibcb->ibcb_code) . '?type=scope&layout=' . $filter_layout) . '" class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                }
            })
            ->order(function ($query) {
                $query->orderbyRaw('CONVERT(branch_group.title USING tis620)')->orderbyRaw('CONVERT(branch.title USING tis620)')->orderbyRaw('CONVERT(standard.tis_tisno USING tis620)');
            })
            ->rawColumns(['checkbox', 'ibcb_name', 'state', 'contact', 'gazette', 'action'])
            ->make(true);
    }

    public function data_ibcb_list(Request $request)
    {
        $filter_search = $request->get('filter_search');
        $filter_status = $request->get('filter_status');
        $filter_tis_id = $request->get('filter_tis_id');
        $filter_branch = $request->get('filter_branch');
        $filter_branch_group = $request->get('filter_branch_group');
        $filter_layout = !empty($request->get('filter_layout')) ? $request->get('filter_layout') : 'app';

        $query = Ibcbs::query()->when($filter_search, function ($query, $filter_search) {
            $search_full = str_replace(' ', '', $filter_search);

            if (strpos($search_full, 'CB-') !== false || strpos($search_full, 'IB-') !== false) {
                return $query->where('ibcb_code', 'LIKE', "%$search_full%");
            } else {
                return $query->where(function ($query2) use ($search_full) {

                    $ids = IbcbsScope::where(function ($query) use ($search_full) {
                        $query->OrwhereHas('bs_branch_group', function ($query) use ($search_full) {
                            $query->where(DB::raw("REPLACE(title,' ','')"), 'LIKE', "%" . $search_full . "%");
                        })
                            ->OrwhereHas('scopes_details.bs_branch', function ($query) use ($search_full) {
                                $query->where(DB::raw("REPLACE(title,' ','')"), 'LIKE', "%" . $search_full . "%");
                            })
                            ->OrwhereHas('scopes_tis.scope_tis_std', function ($query) use ($search_full) {
                                $query->where(function ($query) use ($search_full) {
                                    $query->where(DB::raw("REPLACE(tb3_TisThainame,' ','')"), 'LIKE', "%" . $search_full . "%")->Orwhere(DB::Raw("REPLACE(tb3_Tisno,' ','')"), 'LIKE', "%$search_full%");
                                });
                            });
                    })->select('ibcb_id');

                    $query2->Where(DB::raw("REPLACE(taxid,' ','')"), 'LIKE', "%" . $search_full . "%")
                        ->OrWhere(DB::raw("REPLACE(name,' ','')"), 'LIKE', "%" . $search_full . "%")
                        ->OrWhere(DB::raw("REPLACE(ibcb_code,' ','')"), 'LIKE', "%" . $search_full . "%")
                        ->OrWhere(DB::raw("REPLACE(ibcb_name,' ','')"), 'LIKE', "%" . $search_full . "%")
                        ->OrwhereIn('id', $ids);
                });

            }
        })
            ->when($filter_status, function ($query, $filter_status) {
                if ($filter_status == 1) {
                    $query->whereHas('scopes_group', function ($query) {
                        $query->where('end_date', '>=', date('Y-m-d'));
                    })
                        ->Orwhere(function ($query2) {
                            $query2->whereHas('scopes_group', function ($query) {
                                $query->where('type', 2)->where('state', 1);
                            });
                        })
                        ->Orwhere(function ($query2) {
                            $query2->whereHas('scopes_group', function ($query) {
                                $query->where('type', 2)->where('end_date', '>', date('Y-m-d'));
                            });
                        });
                } else {
                    $query->whereHas('scopes_group', function ($query) {
                        $query->where('end_date', '<', date('Y-m-d'));
                    })
                        ->Orwhere(function ($query2) {
                            $query2->whereHas('scopes_group', function ($query) {
                                $query->where('type', 2)->where('state', '<>', 1);
                            });
                        })
                        ->Orwhere(function ($query2) {
                            $query2->whereHas('scopes_group', function ($query) {
                                $query->where('type', 2)->where('end_date', '<', date('Y-m-d'));
                            });
                        });
                }
            })
            ->when($filter_tis_id, function ($query, $filter_tis_id) {
                $query->whereHas('ibcbs_scope_tis', function ($query) use ($filter_tis_id) {
                    $query->where('tis_id', $filter_tis_id);
                });
            })
            ->when($filter_branch_group, function ($query, $filter_branch_group) {
                $query->whereHas('ibcb_scope', function ($query) use ($filter_branch_group) {
                    $query->where('branch_group_id', $filter_branch_group);
                });
            })
            ->when($filter_branch, function ($query, $filter_branch) {
                $query->whereHas('ibcbs_scope_detail', function ($query) use ($filter_branch) {
                    $query->where('branch_id', $filter_branch);
                });
            });

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('ibcb_code', function ($item) {
                return $item->ibcb_code;
            })
            ->addColumn('ibcb_name', function ($item) {
                $html = !empty($item->ibcb_name) ? $item->ibcb_name : '-';
                // $html .= !empty($item->name) ? '<div>('.$item->name.')</div>' : '';
                $html .= '<div>(ชื่อย่อ: ' . (!empty($item->initial) ? $item->initial : '-') . ')</div>';
                return $html;
            })
            ->addColumn('scope_group', function ($item) {
                return !empty($item->ScopeGroup) ? $item->ScopeGroup : '-';
            })
            ->addColumn('start_date', function ($item) {
                return !empty($item->ibcb_start_date) ? HP::revertDate($item->ibcb_start_date) : '-';
            })
            ->addColumn('end_date', function ($item) {
                if (!empty($item->ibcb_end_date)) {
                    return !empty($item->ibcb_end_date) ? HP::revertDate($item->ibcb_end_date) : '-';
                } else {
                    $end_date = $item->scopes_group()->select('end_date')->max('end_date');
                    return !empty($end_date) ? HP::revertDate($end_date) : '-';
                }
            })
            ->addColumn('state', function ($item) {
                $StateHtml = [1 => '<span class="text-success">Active</span>', 2 => '<span class="text-danger">Not Active</span>'];
                $EndHtml = null;
                if (!empty($item->ibcb_end_date)) {
                    $EndHtml = !empty($item->ibcb_end_date) ? '<div><small>(Exp.' . HP::revertDate($item->ibcb_end_date) . ')</small></div>' : null;
                } else {
                    $end_date = $item->scopes_group()->select('end_date')->max('end_date');
                    $EndHtml = !empty($end_date) ? '<div><small>(Exp.' . HP::revertDate($end_date) . ')<small></div>' : null;
                }
                return (array_key_exists($item->state, $StateHtml) ? $StateHtml[$item->state] : '<span class="text-danger">Not Active</span>') . $EndHtml;
            })
            ->addColumn('gazette', function ($item) {
                $ibcb_scope = $item->scopes_group()->whereNotNull('ref_ibcb_application_no')->get()->last();
                $attach_file_gazette = !empty($ibcb_scope->application_ibcb_board_approve->attach_file_gazette) ? $ibcb_scope->application_ibcb_board_approve->attach_file_gazette : null;
                return !empty($attach_file_gazette) ? '<a href="' . (HP::getFileStorage($attach_file_gazette->url)) . '" target="_blank">' . (HP::FileExtension($attach_file_gazette->filename) ?? '') . '</a>' : '-';
            })
            ->addColumn('action', function ($item) use ($filter_layout) {
                if (!empty($item->ibcb_code)) {
                    return ' <a href="' . url('section5/ibcb/' . base64_encode($item->ibcb_code) . '?type=ibcb&layout=' . $filter_layout) . '" class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                }
            })
            ->order(function ($query) {
                $query->orderBy('id', 'DESC');
            })
            ->rawColumns(['checkbox', 'ibcb_name', 'state', 'contact', 'gazette', 'action', 'scope_group'])
            ->make(true);
    }

    public function ibcb_detail($code, Request $request)
    {
        $type = $request->get('type');
        $ibcb_code = base64_decode($code);
        $ibcb = Ibcbs::where('ibcb_code', $ibcb_code)->first();
        $layout = !empty($request->get('layout')) ? $request->get('layout') : 'app';
        return view('section5/ibcb.detail', compact('ibcb', 'type', 'layout'));
    }


    public function labs_list(Request $request)
    {
        return view('section5/labs.iframe.index');
    }

    public function data_labs_list(Request $request)
    {
        $filter_search = $request->get('filter_search');
        $filter_status = $request->get('filter_status');
        $filter_tis_id = $request->get('filter_tis_id');
        $filter_test_item_id = $request->get('filter_test_item_id');
        $lab_id = $request->get('lab_id');
        $item_no = $request->get('item_no');
        $filter_trader = $request->get('filter_trader');

        $filter_layout = !empty($request->get('filter_layout')) ? $request->get('filter_layout') : 'app';


        $query = Labs::query()
            ->when($lab_id, function ($query, $lab_id) {
                return $query->where('id', $lab_id);
            })
            ->when($item_no, function ($query, $item_no) {
                return $query->where('ref_lab_application_no', $item_no);
            })
            ->when($filter_trader, function ($query) {
                $user = auth()->user();
                if (!is_null($user)) {
                    $user_act_instead = $user->ActInstead;
                    $tax_id = is_null($user_act_instead) ? $user->tax_number : $user_act_instead->tax_number;
                    if (!empty($tax_id)) {
                        return $query->where('taxid', $tax_id);
                    }
                }
            })
            ->when($filter_search, function ($query, $filter_search) {
                $search_full = str_replace(' ', '', $filter_search);

                if (strpos($search_full, 'LAB-') !== false) {
                    return $query->where('lab_code', 'LIKE', "%$search_full%");
                } else {
                    return $query->where(function ($query2) use ($search_full) {

                        $query2->Where(DB::raw("REPLACE(taxid,' ','')"), 'LIKE', "%" . $search_full . "%")
                            ->OrWhere(DB::raw("REPLACE(name,' ','')"), 'LIKE', "%" . $search_full . "%")
                            ->OrWhere(DB::raw("REPLACE(lab_code,' ','')"), 'LIKE', "%" . $search_full . "%")
                            ->OrWhere(DB::raw("REPLACE(lab_name,' ','')"), 'LIKE', "%" . $search_full . "%")
                            ->OrWhere(function ($query) use ($search_full) {
                                $std_id = LabsScope::where(function ($query) use ($search_full) {
                                    $query->whereHas('tis_standards', function ($query) use ($search_full) {
                                        $query->where(function ($query) use ($search_full) {
                                            $query->where(DB::raw("REPLACE(tb3_TisThainame,' ','')"), 'LIKE', "%" . $search_full . "%")->Orwhere(DB::Raw("REPLACE(tb3_Tisno,' ','')"), 'LIKE', "%$search_full%");
                                        });
                                    });
                                })
                                    ->select('lab_id');
                                $query->whereIn('id', $std_id);
                            })
                            ->OrWhere(function ($query) use ($search_full) {
                                $test_item_id = LabsScope::where(function ($query) use ($search_full) {
                                    $query->whereHas('test_item', function ($query) use ($search_full) {
                                        $query->where(DB::raw("REPLACE(title,' ','')"), 'LIKE', "%" . $search_full . "%");
                                    });
                                })
                                    ->select('lab_id');
                                $query->whereIn('id', $test_item_id);
                            });

                    });

                }
            })
            ->when($filter_status, function ($query, $filter_status) {
                if ($filter_status == 1) {
                    $query->whereHas('scope_standard', function ($query) {
                        $query->where('end_date', '>=', date('Y-m-d'));
                    });
                } else {

                    $query->Has('scope_standard', '==', 0)
                        ->OrWhere('state', '<>', 1)
                        ->OrwhereHas('scope_standard', function ($query) {
                            $query->where('end_date', '<', date('Y-m-d'));
                        });

                }
            })
            ->when($filter_tis_id, function ($query, $filter_tis_id) {
                $query->whereHas('scope_standard', function ($query) use ($filter_tis_id) {
                    $query->where('tis_id', $filter_tis_id);
                });
            })
            ->when($filter_test_item_id, function ($query, $filter_test_item_id) {

                if (is_array($filter_test_item_id)) {
                    $query->whereHas('scope_standard', function ($query) use ($filter_test_item_id) {
                        $query->whereIn('test_item_id', $filter_test_item_id);
                    });
                }

            });


        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('lab_code', function ($item) {
                return !empty($item->lab_code) ? $item->lab_code : '-';
            })
            ->addColumn('lab_name', function ($item) {
                $html = !empty($item->lab_name) ? $item->lab_name : '-';
                $html .= '<div>(' . (!empty($item->taxid) ? $item->taxid : '<em class="text-muted">ไม่ระบุเลขนิติบุคคล</em>') . ')</div>';
                return $html;
            })
            ->addColumn('start_date', function ($item) {
                return !empty($item->lab_start_date) ? HP::revertDate($item->lab_start_date) : '-';
            })
            ->addColumn('end_date', function ($item) {
                if (!empty($item->lab_end_date)) {
                    return !empty($item->lab_end_date) ? HP::revertDate($item->lab_end_date) : '-';
                } else {
                    $end_date = $item->scope_standard()->select('end_date')->max('end_date');
                    return !empty($end_date) ? HP::revertDate($end_date) : '-';
                }
            })
            ->addColumn('standards', function ($item) {
                return !empty($item->ScopeStandardHtml) ? $item->ScopeStandardHtml : '-';
            })
            ->addColumn('state', function ($item) {

                $StateHtml = [1 => '<span class="text-success">Active</span>', 2 => '<span class="text-danger">Not Active</span>'];
                $max_data = $item->scope_standard()->whereNotNull('end_date')->orderBy('end_date', 'desc')->first();

                if (!empty($max_data)) {
                    return (!empty($max_data->end_date) && $max_data->end_date >= date('Y-m-d')) && array_key_exists($max_data->state, $StateHtml) ? $StateHtml[$max_data->state] : '<span class="text-danger">Not Active</span>';
                } else {
                    return '<span class="text-danger">Not Active</span>';
                }
            })
            ->addColumn('gazette', function ($item) {
                $ibcb_scope = $item->scope_standard()->whereNotNull('ref_lab_application_no')->get()->last();
                $attach_file_gazette = !empty($ibcb_scope->application_board_approve->attach_file_gazette) ? $ibcb_scope->application_board_approve->attach_file_gazette : null;
                return !empty($attach_file_gazette) ? '<a href="' . (HP::getFileStorage($attach_file_gazette->url)) . '" target="_blank">' . (HP::FileExtension($attach_file_gazette->filename) ?? '') . '</a>' : '-';
            })
            ->addColumn('action', function ($item) {
                return ' <a href="' . url('request-section-5/labs/show/' . $item->id) . '" class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            })
            ->rawColumns(['checkbox', 'action', 'lab_name', 'state', 'gazette', 'standards'])
            ->make(true);
    }

    public function data_ibcbs_list(Request $request)
    {
        $filter_search = $request->get('filter_search');
        $filter_status = $request->get('filter_status');
        $ibcb_id = $request->get('ibcb_id');
        $item_no = $request->get('item_no');
        $filter_trader = $request->get('filter_trader');

        $query = Ibcbs::query()
            ->when($ibcb_id, function ($query, $ibcb_id) {
                return $query->where('id', $ibcb_id);
            })
            ->when($item_no, function ($query, $item_no) {
                return $query->where('ref_ibcb_application_no', $item_no);
            })
            ->when($filter_trader, function ($query) {
                $user = auth()->user();
                if (!is_null($user)) {
                    $user_act_instead = $user->ActInstead;
                    $tax_id = is_null($user_act_instead) ? $user->tax_number : $user_act_instead->tax_number;
                    if (!empty($tax_id)) {
                        return $query->where('taxid', $tax_id);
                    }
                }
            })
            ->when($filter_search, function ($query, $filter_search) {
                $search_full = str_replace(' ', '', $filter_search);

                if (strpos($search_full, 'IB-') !== false || strpos($search_full, 'CB-') !== false) {
                    return $query->where('ibcb_code', 'LIKE', "%$search_full%");
                } else {
                    return $query->where(function ($query2) use ($search_full) {
                        $query2->Where(DB::raw("REPLACE(taxid,' ','')"), 'LIKE', "%" . $search_full . "%")
                            ->OrWhere(DB::raw("REPLACE(name,' ','')"), 'LIKE', "%" . $search_full . "%")
                            ->OrWhere(DB::raw("REPLACE(ibcb_code,' ','')"), 'LIKE', "%" . $search_full . "%")
                            ->OrWhere(DB::raw("REPLACE(ibcb_name,' ','')"), 'LIKE', "%" . $search_full . "%");
                    });
                }
            })
            ->when($filter_status, function ($query, $filter_status) {
                if ($filter_status == 1) {
                    $query->where('state', 1);
                } else {
                    $query->where('state', '<>', 1);
                }
            });

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('ibcb_code', function ($item) {
                return !empty($item->ibcb_code) ? $item->ibcb_code : '-';
            })
            ->addColumn('ibcb_name', function ($item) {
                $html = !empty($item->ibcb_name) ? $item->ibcb_name : '-';
                $html .= '<div>(' . (!empty($item->taxid) ? $item->taxid : '<em class="text-muted">ไม่ระบุเลขนิติบุคคล</em>') . ')</div>';
                return $html;
            })
            ->addColumn('start_date', function ($item) {
                return !empty($item->ibcb_start_date) ? HP::revertDate($item->ibcb_start_date) : '-';
            })
            ->addColumn('end_date', function ($item) {
                return !empty($item->ibcb_end_date) ? HP::revertDate($item->ibcb_end_date) : '-';
            })
            ->addColumn('standards', function ($item) {
                return !empty($item->ScopeGroup) ? $item->ScopeGroup : '-';
            })
            ->addColumn('state', function ($item) {
                $StateHtml = [1 => '<span class="text-success">Active</span>', 2 => '<span class="text-danger">Not Active</span>'];
                return array_key_exists($item->state, $StateHtml) ? $StateHtml[$item->state] : '<span class="text-danger">Not Active</span>';
            })
            ->addColumn('action', function ($item) {
                return ' <a href="' . url('request-section-5/ibcbs/show/' . $item->id) . '" class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            })
            ->rawColumns(['checkbox', 'action', 'ibcb_name', 'state', 'standards'])
            ->make(true);
    }



    public function GetDataTestItem($testitem, $test_item_id, $scope_list)
    {
        $level = 0;
        $list = $this->LoopItem($testitem, $level, $test_item_id);

        return $list;

    }

    public function LoopItem($testitem, $level, $test_item_id)
    {
        $level++;
        $i = 0;
        $html = '';
        if ($level == 1) {
            $html .= '<ul class="list-unstyled">';
        } else {
            $html .= '<ul style="list-style-type: none;">';
        }

        foreach ($testitem as $item) {
            ++$i;

            if (count($item->TestItemParentData) >= 1) {
                $html .= '<li>' . ((!empty($item->no) ? 'ข้อ ' . $item->no . ' ' : '') . $item->title) . '</li>';
                $result = $this->LoopItem($item->TestItemParentData, $level, $test_item_id);
                $html .= $result;
            } else {
                if (in_array($item->id, $test_item_id)) {
                    $html .= '<li>' . ((!empty($item->no) ? 'ข้อ ' . $item->no . ' ' : '') . $item->title) . '</li>';
                }
            }

        }

        $html .= '</ul>';

        return $html;
    }

    public function welcome_labs_list(Request $request)
    {
        return view('section5/labs.index-welcome');
    }

    public function GetTestItem($tis_id)
    {

        if (!empty($tis_id) && is_numeric($tis_id)) {

            $main = TestItem::where('tis_id', $tis_id)
                ->where('type', 1)
                ->with('main_test_item_parent_data')
                ->get();

            $list = [];
            foreach ($main as $mains) {

                $parent = $mains->main_test_item_parent_data()
                    ->where(function ($query) {
                        $query->where('input_result', 1)->Orwhere('test_summary', 1);
                    })
                    ->get();

                if (count($parent) >= 1) {

                    foreach ($parent as $parents) {
                        $data = new stdClass;
                        $data->id = $parents->id;
                        $data->title = (!empty($parents->no) ? $parents->no . ' ' : '') . $parents->title . ' <em>(ภายใต้หัวข้อทดสอบ ' . ((!empty($mains->no) ? $mains->no . ' ' : null) . $mains->title) . ')</em>';
                        $list[] = $data;
                    }

                }
            }

            return response()->json($list);

        }

    }
}
