<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Counsel;
use Illuminate\Http\Request;

class CounselsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function index(Request $request)
    {
        return view('counsels.create');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('counsels.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        try {

            $request->request->add(['created_by' => auth()->user()->getKey()]);
            $requestData = $request->all();
            
            Counsel::create($requestData);
            return redirect('counsels')->with('flash_message', 'Counsel added!');

        } catch (\Exception $e) {

            echo $e->getMessage();
            exit;
            return redirect('counsels')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $counsel = Counsel::findOrFail($id);
        return view('counsels.show', compact('counsel'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $counsel = Counsel::findOrFail($id);
        return view('counsels.edit', compact('counsel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        try {

            $request->request->add(['updated_by' => auth()->user()->getKey()]);
            $requestData = $request->all();
            
            $counsel = Counsel::findOrFail($id);
            $counsel->update($requestData);

            return redirect('counsels')->with('flash_message', 'Counsel updated!');

        } catch (\Exception $e) {

            echo $e->getMessage();
            exit;
            return redirect('counsels')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Counsel::destroy($id);
        return redirect('counsels')->with('flash_message', 'Counsel deleted!');
    }
}
