<?php

namespace App\Http\Controllers\Masters;

use App\Models\TimelineMaster;
use App\Models\TimelineTemplate;
use Illuminate\Http\Request;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\AppConstant;

class TimelineTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {

        $timelinetemps = TimelineTemplate::getAllTemplate();
        $systemrevs = TimelineTemplate::getUniqueSysRev();
        $tltemphigh = $tltemplow = $tltempjlf = array();
        foreach($systemrevs as $systemrev) {
            $i = 0;
            $j = 0;
            $k = 0;
            foreach($timelinetemps as $timelinetemp) {
                if($timelinetemp->timelinemasterid == $systemrev->timelinemasterid && $timelinetemp->highexposure == 0) {
                    $tltemphigh[$timelinetemp->timelinemasterid][$i] = $timelinetemp;
                    $i++;
                }

                if($timelinetemp->timelinemasterid == $systemrev->timelinemasterid && $timelinetemp->highexposure == 1) {
                    $tltemplow[$timelinetemp->timelinemasterid][$j] = $timelinetemp;
                    $j++;
                }

                if($timelinetemp->timelinemasterid == $systemrev->timelinemasterid && $timelinetemp->highexposure == 2) {
                    $tltempjlf[$timelinetemp->timelinemasterid][$k] = $timelinetemp;
                    $k++;
                }
            }
        }
        $systemrevname = TimelineMaster::getTimelineMasterByName();
        return view('timelinetemplate/indext2')
            ->with('tltemphigh', $tltemphigh)
            ->with('tltemplow', $tltemplow)
            ->with('tltempjlf', $tltempjlf)
            ->with('systemrevname', $systemrevname);
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
        $timelineid = Input::get('timelineid');
        if($timelineid) {
            Session::flash('isupdate',1);
        }else {
            Session::flash('isupdate',0);
        }
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'stage'       => 'required|string|max:255',
            'expirydays'      => 'required|integer|digits_between:1,5',
            'highexposure'      => 'required|integer|digits_between:1,1',
            'timelinemasterid'      => 'required',
            'srno'      => 'required|integer|digits_between:1,10'
        );
        $messages = $this->validationMessages();
        $validator = Validator::make(Input::all(), $rules, $messages);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('timelinetemplate')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $timelinetempdata = Input::all();
            if($timelineid) {
                TimelineTemplate::updateById($timelinetempdata);
                Session::flash('success', 'Timeline Template Updated Successfully !');
            }else {
                TimelineTemplate::createTimelineTemp($timelinetempdata);
                Session::flash('success', 'Timeline Template Created Successfully !');
            }
            return redirect::to('timelinetemplate');
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
        $timelinetemps = TimelineTemplate::findById($id);
        $systemrevname = TimelineMaster::getTimelineMasterByName();
        return array('timelinetemps' => $timelinetemps,'systemrevname' => $systemrevname);
//        return view('timelinetemplate/edit')
//            ->with('timelinetemps', $timelinetemps);
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
            'stage'       => 'required|string|max:255',
            'expirydays'      => 'required|integer|digits_between:1,5',
            'highexposure'      => 'required|integer|digits_between:1,1',
            'systemrev'      => 'required|integer|digits_between:1,1',
            'srno'      => 'required|integer|digits_between:1,10'
        );
        $messages = $this->validationMessages();
        $validator = Validator::make(Input::all(), $rules, $messages);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('timelinetemplate')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $timelinetempdata = Input::get('data');

            return redirect::to('timelinetemplate');
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

    }

    public function indext2()
    {
        $timelinetemps = TimelineTemplate::getAllTemplate();
        $systemrevs = TimelineTemplate::getUniqueSysRev();
        $tltemphigh = $tltemplow = $tltempjlf = array();
//        dd($systemrevs);
        foreach($systemrevs as $systemrev) {
            $i = 0;
            $j = 0;
            $k = 0;
            foreach($timelinetemps as $timelinetemp) {
                if($timelinetemp->timelinemasterid == $systemrev->timelinemasterid && $timelinetemp->highexposure == 0) {
                    $tltemphigh[$timelinetemp->timelinemasterid][$i] = $timelinetemp;
                    $i++;
                }

                if($timelinetemp->timelinemasterid == $systemrev->timelinemasterid && $timelinetemp->highexposure == 1) {
                    $tltemplow[$timelinetemp->timelinemasterid][$j] = $timelinetemp;
                    $j++;
                }

                if($timelinetemp->timelinemasterid == $systemrev->timelinemasterid && $timelinetemp->highexposure == 2) {
                    $tltempjlf[$timelinetemp->timelinemasterid][$k] = $timelinetemp;
                    $k++;
                }
            }
        }
        return view('timelinetemplate/indext2')
            ->with('tltemphigh', $tltemphigh)
            ->with('tltemplow', $tltemplow)
            ->with('tltempjlf', $tltempjlf);
    }

    /**
     * @return array
     */
    public function validationMessages()
    {
        $messages = array('stage.required' => 'The Stage Name field is required.',
            'stage.string' => 'The Stage Name must be an string.',
            'stage.max' => 'The Stage Name may not be greater than 255 characters.',
            'expirydays.required' => 'The Expiry Days field is required.',
            'expirydays.integer' => 'The Expiry Days must be an integer.',
            'expirydays.digits_between' => 'The Expiry Days must be between 1 and 5 digits.',
            'highexposure.required' => 'The High Exposure field is required.',
            'highexposure.integer' => 'The High Exposure must be an integer.',
            'highexposure.digits_between' => 'The High Exposure must be of 1 digit.',
            'systemrev.required' => 'The System Revision field is required.',
            'systemrev.integer' => 'The System Revision must be an integer.',
            'systemrev.digits_between' => 'The System Revision must be of 1 digit.',
            'srno.required' => 'The Sr No field is required.',
            'srno.integer' => 'The Sr No must be an integer.',
            'srno.digits_between' => 'The Sr No must be be between 1 and 5 digits.',
            'timelinemasterid.required' => 'The System Revision field is required.'
        );
        return $messages;
    }
    public function getHighexpoBySysrev()
    {
        $highexposer = Input::get('highexpo');
        $sysrevs = TimelineMaster::getSysRevByHexpo($highexposer);
        return $sysrevs;
    }
}
