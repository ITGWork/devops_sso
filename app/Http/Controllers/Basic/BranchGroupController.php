<?php

namespace App\Http\Controllers\Basic;

use App\Http\Controllers\Controller;
use App\Models\Basic\BranchGroup;
use Illuminate\Http\Request;

class BranchGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [];
        $keyword = $request->get('search');
        $filter['filter_state'] = $request->get('filter_state', '');
        $filter['perPage'] = $request->get('perPage', 10);

        $branchGroup = new BranchGroup;

        if (!empty($filter['filter_state'])) {
            $branchGroup = $branchGroup->where('state', $filter['filter_state']);
        }

        $branchGroup = $branchGroup->sortable()->with('user_created')
                                                    ->with('user_updated')
                                                    ->paginate($filter['perPage']);

        return view('basic.branch-groups.index', compact('branchGroup', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('basic.branch-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $request->request->add(['created_by' => auth()->user()->getKey()]); //user create
        $requestData = $request->all();

        BranchGroup::create($requestData);
        return redirect('basic/branch-groups')->with('flash_message', 'เพิ่ม branch-groups เรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Basic\BranchGroup  $branchGroup
     * @return \Illuminate\Http\Response
     */
    public function show(BranchGroup $branchGroup)
    {
        return view('basic.branch-groups.show', compact('branchGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Basic\BranchGroup  $branchGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(BranchGroup $branchGroup)
    {
        return view('basic.branch-groups.edit', compact('branchGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Basic\BranchGroup  $branchGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BranchGroup $branchGroup)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $request->request->add(['updated_by' => auth()->user()->getKey()]); //user update
        $requestData = $request->all();

        $branchGroup->update($requestData);

        return redirect('basic/branch-groups')->with('flash_message', 'แก้ไข branch-groups เรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Basic\BranchGroup  $branchGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
       
        $requestData = $request->all();

        if(array_key_exists('cb', $requestData)){
          $ids = $requestData['cb'];
          $db = new BranchGroup;
          BranchGroup::whereIn($db->getKeyName(), $ids)->delete();
        }else{
            BranchGroup::destroy($id);
        }

        return redirect('basic/branch-groups')->with('flash_message', 'ลบข้อมูลเรียบร้อยแล้ว!');
    }
    
    /*
      **** Update State ****
    */
    public function update_state(Request $request){

        $requestData = $request->all();

        if(array_key_exists('cb', $requestData)){
            $ids = $requestData['cb'];
            $db = new BranchGroup;
            BranchGroup::whereIn($db->getKeyName(), $ids)->update(['state' => $requestData['state']]);
        }

        return redirect('basic/branch-groups')->with('flash_message', 'แก้ไขข้อมูลเรียบร้อยแล้ว!');
    }

}
