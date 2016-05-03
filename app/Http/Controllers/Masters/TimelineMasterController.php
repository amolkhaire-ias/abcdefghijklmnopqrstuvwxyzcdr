<?php

namespace App\Http\Controllers\Masters;

use App\Models\TimelineMaster;
use App\Models\TimelineTemplate;
use Illuminate\Http\Request;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
class TimelineMasterController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {

       $timelinemaster = TimelineMaster::all();
        return view('timelinemaster/index')
            ->with('timelinemasters', $timelinemaster);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('timelinetemplate/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required|string|max:255',
            'stages'      => 'required|integer',
//            'days'      => 'required|integer',

        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('timelinemaster')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $timelinetempdata = new TimelineMaster();
            $timelinetempdata->name = Input::get('name');
            $timelinetempdata->stages = Input::get('stages');
//            $timelinetempdata->days = Input::get('days');
            $timelinetempdata->highexposure = Input::get('highexposure');
            $timelinetempdata->description = Input::get('description');
            $timelinetempdata->save();
            Session::flash('success', 'Timeline Master Created Successfully !');
            return redirect::to('timelinemaster');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $timelinetemps = TimelineTemplate::findById($id);
        return view('timelinetemplate/show')
            ->with('timelinetemps', $timelinetemps);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $timelinetemps = TimelineTemplate::find($id);
        return view('timelinetemps/edit')
            ->with('timelinetemps', $timelinetemps);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = array(
            'data.stage'       => 'required|string|max:255',
            'data.expirydays'      => 'required|integer|digits_between:1,5',
            'data.highexposure'      => 'required|integer|digits_between:1,1',
            'data.systemrev'      => 'required|integer|digits_between:1,1',
            'data.srno'      => 'required|integer|digits_between:1,10'
        );
        $messages = $this->validationMessages();
        $validator = Validator::make(Input::all(), $rules, $messages);
        // process the login
        if ($validator->fails()) {
            return response(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400);
        } else {
            // store
            $timelinetempdata = Input::get('data');
            TimelineTemplate::updateById($timelinetempdata);
            Session::flash('success', 'Timeline Master Updated Successfully !');
            return response(array('success' => true,
                'successmsg', 'Timeline Template Updated Successfully !'), 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect::to('timelinemaster');
    }



    /**
     * @return array
     */
}
