<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Announcement;
use DB;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $announcement = DB::table("announcements")->get();
        
        return view('backend.product.announcement.index',['announcement' => $announcement]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $announ =  DB::table('announcements')
                    ->where('id', '=', $id)
                    ->get();
        return view('backend.product.announcement.edit',['annmt' => $announ]);
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
public function update(Request $request, $id)
    {
        $anmnt = Announcement::findOrFail($id);
        $anmnt->name = $request->name;
        $anmnt->logo = $request->logo;
        $anmnt->status = $request->status;
        $anmnt->save();

        flash(translate('Announcement has been updated successfully'))->success();
        return back();

    }

}
