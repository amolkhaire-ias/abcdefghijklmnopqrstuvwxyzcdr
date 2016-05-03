<?php
namespace App;
namespace App\Http\Controllers\Utility;

use App\Models\Utility;
use App\Models\UtilityDoc;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Country;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class UtilityController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
//        $utility = Utility::all();
        $utilitydocs = UtilityDoc::all();
        return view('utility.index')
            ->with('utilitydocs',$utilitydocs);
            
    }

     /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function updatedoc()
    {
        $utility = Utility::all();
        return view('utility.updatedoc')
            ->with('utility',$utility);
            
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listdoc()
    {
        // load the view and pass the nerds
        return view('utility.listdoc');
            
    }
    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $utility = Utility::all();
        return view('utility.create')
            ->with('utility',$utility);
    }


    public function store(Request $request)
    {
        $files = Input::file('images');
        // Making counting of uploaded images
        $file_count = count($files);
        // start count how many uploaded
        $uploadcount = 0;
        foreach($files as $file) {
            $rules = array('file' => 'required'); //'required|mimes:png,gif,jpeg,txt,pdf,doc'
            $validator = Validator::make(array('file'=> $file), $rules);
            if($validator->passes()){
                $file = $request->file('file1');
                $extension = $file->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $filename = $file->getClientOriginalName();
                $upload_success = $file->move($destinationPath, $filename);
                if($upload_success) {
                $entry = new UtilityDoc();
                $entry->type = $file->getClientMimeType();
                $entry->name = $file->getClientOriginalName();
                $entry->extension = $file->getFilename() . '.' . $extension;
                $entry->content = $file->getFilename() . '.' . $extension;
                $entry->save();
                $uploadcount ++;
            }
            }
        }
        if($uploadcount == $file_count){
            Session::flash('success', 'Upload successfully');
            return Redirect::to('upload');
        }
        else {
            return Redirect::to('upload')->withInput()->withErrors($validator);
        }



        $utility = new Utility();
        $utility->name = Input::get('name');
        $utility->criticaldocstatus = $docstatus;
        $utility->doccollected = Input::get('doccollected');
        $utility->docverified = Input::get('docverified');
        $utility->docid = Input::get('docid');
        $utility->save();
        Session::flash('message', 'Successfully created Monitor!');
        // return Redirect::back();
        return Redirect::to('monitoring');
       
    }

    public function upload (Request $request)
    {
        $images = $request->file('images');
        $file_count = count($images);
        $uploadcount = 0;
        foreach($images as $file) {
            $rules = array('file' => 'required'); //'required|mimes:png,gif,jpeg,txt,pdf,doc'
            $validator = Validator::make(array('file'=> $file), $rules);
            if($validator->passes()){
                $extension = $file->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $filename = $file->getClientOriginalName();
                $upload_success = $file->move($destinationPath, $filename);
                if($upload_success) {
                    $entry = new UtilityDoc();
                    $entry->type = $file->getClientMimeType();
                    $entry->name = $file->getClientOriginalName();
                    $entry->extension = $file->getFilename() . '.' . $extension;
                    $entry->content = $file->getFilename() . '.' . $extension;
                    $namefield = Input::get('namefield'.$file);
                    $entry->namefield = $namefield ? $namefield : 'name not fount';
                    $entry->save();
                    $uploadcount ++;
                }
            }
        }
        if($uploadcount == $file_count){
            Session::flash('success', 'Upload successfully');
            return Redirect::to('utility');
        }
        else {
            return Redirect::to('utility')->withInput()->withErrors($validator);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
    
        // show the view and pass the nerd to it
        return view('utility.listdoc');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
     

        // show the edit form and pass the nerd
        return view('utility.listdoc');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
            return Redirect::to('utility.listdoc');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        return Redirect::to('utility.listdoc');
    }



}
