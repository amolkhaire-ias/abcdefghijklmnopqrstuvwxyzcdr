<?php
namespace App\Helpers;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Created by Pradeep.
 * Date: 2/16/2016
 * Time: 4:21 PM
 */

class HelperServiceProvider
{
    public static function getAuthUser(){
        return Auth::user()->id;
    }

    public static function putInSession($key,$value){
        Session::put($key,$value);
    }

    public static function forgetFromSession($key){
        Session::forget($key);
    }

    public static function getCompanyName(){
        $cmpid = Session::get('cmpid');
        $companyName = Company::select('name')->where('id',$cmpid)->first();
        return $companyName['name'];
    }

    public static function postParams(){

    }
    public static function createTreeView($shareArray, $currentParent, $currLevel = 0, $prevLevel = -1) {
        foreach ($shareArray as $categoryId => $category) {
            if ($currentParent == $category['rootid']) {
                if ($currLevel > $prevLevel){
                    echo " <ul class='tree'> ";
                }
                if ($currLevel == $prevLevel){
                    echo " </li> ";
                }
//                echo '<input type="hidden" class="abc" value="'.$category['id'].'"/>';
                echo '<li class="temp123" id="'.$category['id'].'">'.'<span class="shareholdercat" id="'.$category['id'].'">'.$category['codeId'].":".$category['category'].'</span>';
                if ($currLevel > $prevLevel) {
                    $prevLevel = $currLevel;
                }
                $currLevel++;
                HelperServiceProvider::createTreeView ($shareArray, $categoryId, $currLevel, $prevLevel);
                $currLevel--;
            }
        }
        if ($currLevel == $prevLevel){
            echo " </li>  </ul> ";
        }
    }

    public static function createTree($shareArray, $currentParent, $currLevel = 0, $prevLevel = -1) {
        foreach ($shareArray as $categoryId => $category) {
            if ($currentParent == $category['rootid']) {
                if ($currLevel > $prevLevel){

                    echo " <ul class='tree firstul'> ";
                }
                if ($currLevel == $prevLevel){
                    echo " </li> ";
                }
                echo '<li class="temp123 col-sm-12" id="'.$category['id'].'">'.'<span class="shareholdercat" style="float:left;max-width: 41%;" id="'.$category['id'].'">'.$category['codeId'].":".$category['category'].'<input type="hidden" name="sid'.$category['id'].'" value="'.$category['sdid'].'"><input type="hidden" name="level'.$category['id'].'" value="'.$category['id'].'"></span>'.'<div class="col-sm-6 firstdiv"  style="float:right">

                        <input type=text name="A'.$category['id'].'" class="col-sm-11 dimention form-control text-align-right numval" value="'.$category['shareholdernum'].'"/>'
                        .'<input type=text name="B'.$category['id'].'" class="col-sm-12 dimention form-control text-align-right numval totalshare1" value="'.$category['totalshares'].'"/>'
                        .'<input type=text name="C'.$category['id'].'" class="col-sm-12 dimention form-control text-align-right numval" value="'.$category['dematerializeform'].'"/>'
                        .'<input type=text name="D'.$category['id'].'" class="col-sm-12 dimention form-control text-align-right numval totalshare2" value="'.$category['numofshares'].'"/>'
                        .'<input type=text name="E'.$category['id'].'" class="col-sm-12 dimention form-control text-align-right numval totalshare3" value="'.$category['percentage'].'" readonly/>'.'</div>';
                if ($currLevel > $prevLevel) {
                    $prevLevel = $currLevel;
                }
                $currLevel++;
                HelperServiceProvider::createTree ($shareArray, $categoryId, $currLevel, $prevLevel);
                $currLevel--;
            }
        }
        if ($currLevel == $prevLevel){
            echo " </li>  </ul> ";
        }
    }


    public static function createTreePending($shareArray, $currentParent, $currLevel = 0, $prevLevel = -1) {
        foreach ($shareArray as $categoryId => $category) {
            if ($currentParent == $category['rootid']) {
                if ($currLevel > $prevLevel){

                    echo " <ul class='tree firstul'> ";
                }
                if ($currLevel == $prevLevel){
                    echo " </li> ";
                }
                echo '<li class="temp123 innerli col-sm-12" id="'.$category['id'].'">'.'<span class="shareholdercat" style="float:left;max-width: 41%;" id="'.$category['id'].'">'.$category['codeId'].":".$category['category'].'<input type="hidden" name="sid'.$category['id'].'" value="'.$category['sdid'].'"><input type="hidden" name="level'.$category['id'].'" value="'.$category['id'].'"></span>'.'<div class="col-sm-6 firstdiv"  style="float:right">
                        <input type=text name="A'.$category['id'].'" class="col-sm-11 dimention form-control text-align-right" value="'.$category['shareholdernum'].'"/>'
                    .'<input type=text name="B'.$category['id'].'" class="col-sm-12 dimention form-control text-align-right" value="'.$category['totalshares'].'"/>'
                    .'<input type=text name="C'.$category['id'].'" class="col-sm-12 dimention form-control text-align-right" value="'.$category['dematerializeform'].'"/>'
                    .'<input type=text name="D'.$category['id'].'" class="col-sm-12 dimention form-control text-align-right" value="'.$category['numofshares'].'"/>'
                    .'<input type=text name="E'.$category['id'].'" class="col-sm-12 dimention form-control text-align-right" value="'.$category['percentage'].'" readonly/>'.'</div>';
                if ($currLevel > $prevLevel) {
                    $prevLevel = $currLevel;
                }
                $currLevel++;
                HelperServiceProvider::createTree ($shareArray, $categoryId, $currLevel, $prevLevel);
                $currLevel--;
            }
        }
        if ($currLevel == $prevLevel){
            echo " </li>  </ul> ";
        }
    }

    public static function createTreeIndex($shareArray, $currentParent, $currLevel = 0, $prevLevel = -1) {
        foreach ($shareArray as $categoryId => $category) {
            if ($currentParent == $category['rootid']) {
                if ($currLevel > $prevLevel){

                    echo " <ul class='tree firstul'> ";
                }
                if ($currLevel == $prevLevel){
                    echo " </li> ";
                }
                echo '<li class="temp123 col-sm-12" id="'.$category['id'].'">'.'<span class="shareholdercat" style="float:left;max-width: 41%;" id="'.$category['id'].'">'.$category['codeId'].":".$category['category'].'<input type="hidden" name="sid'.$category['id'].'" value="'.$category['sdid'].'"><input type="hidden" name="level'.$category['id'].'" value="'.$category['id'].'"></span>'.
                    '<div class="col-sm-6 textpad"  style="float:right">
                     <input type=text name="A'.$category['id'].'" class="dimention form-control text-align-right" value="'.$category['shareholdernum'].'" readonly/>'
                    .'<input type=text name="B'.$category['id'].'" class="dimention form-control text-align-right" value="'.$category['totalshares'].'" readonly/>'
                    .'<input type=text name="C'.$category['id'].'" class="dimention form-control text-align-right" value="'.$category['dematerializeform'].'" readonly/>'
                    .'<input type=text name="D'.$category['id'].'" class="dimention form-control text-align-right" value="'.$category['numofshares'].'" readonly/>'
                    .'<input type=text name="E'.$category['id'].'" class="dimention form-control text-align-right" value="'.$category['percentage'].'" readonly/>'.'</div>';
                if ($currLevel > $prevLevel) {
                    $prevLevel = $currLevel;
                }
                $currLevel++;
                HelperServiceProvider::createTreeIndex($shareArray, $categoryId, $currLevel, $prevLevel);
                $currLevel--;
            }
        }
        if ($currLevel == $prevLevel){
            echo " </li>  </ul> ";
        }
    }

    public static function createTreeDisplay($shareArray, $currentParent, $currLevel = 0, $prevLevel = -1) {
        $shareholdnum = $totalshare = $demater = $numofshare = $percent = $temp = 0;
        foreach ($shareArray as $categoryId => $category) {
            $shareholdnum = $shareholdnum + $category['shareholdernum'];
            $totalshare = $totalshare + $category['totalshares'];
            $demater = $demater + $category['dematerializeform'];
            $numofshare = $numofshare + $category['numofshares'];
            $percent = $percent + $category['percentage'];
            if ($currentParent == $category['rootid']) {
                if ($currLevel > $prevLevel){
                    echo " <ul class='tree firstul'> ";
                }
                if ($currLevel == $prevLevel){
                    echo " </li> ";
                }
                $perc = 0.00;
                if($category['numofshares'] > 0){
                    $perc = ($category['numofshares']/$category['totalshares'])*100;
                }
                echo '<li class="temp123 col-sm-12" id="'.$category['id'].'">'.'<span class="shareholdercat" style="float:left;max-width: 41%;" id="'.$category['id'].'">'.$category['codeId'].":".$category['category'].'<input type="hidden" name="sid'.$category['id'].'" value="'.$category['sdid'].'"><input type="hidden" name="level'.$category['id'].'" value="'.$category['id'].'"></span>'.
                    '<div class="col-sm-6 textpad total"  style="float:right">
                     <input type=text name="A'.$category['id'].'" class="shareA dimention form-control text-align-right" value="'.$category['shareholdernum'].'" readonly/>'
                    .'<input type=text name="B'.$category['id'].'" class="shareB dimention form-control text-align-right" value="'.$category['totalshares'].'" readonly/>'
                    .'<input type=text name="C'.$category['id'].'" class="shareC dimention form-control text-align-right" value="'.$category['dematerializeform'].'" readonly/>'
                    .'<input type=text name="D'.$category['id'].'" class="shareD dimention form-control text-align-right" value="'.$category['numofshares'].'" readonly/>'
                    .'<input type=text name="E'.$category['id'].'" class="shareE dimention form-control text-align-right" value="'.round($perc,2).'" readonly/>'.'</div>';
                if ($currLevel > $prevLevel) {$temp = $shareholdnum + $category['shareholdernum'];
                    $prevLevel = $currLevel;
                }
                $currLevel++;
                HelperServiceProvider::createTreeDisplay($shareArray, $categoryId, $currLevel, $prevLevel);
                $currLevel--;
            }
        }
        if ($currLevel == $prevLevel){
            echo " </li>  </ul> ";
        }
        echo '<input type="hidden" class="nosh" value="'.$shareholdnum.'"/>';
        echo '<input type="hidden" class="tsh" value="'.$totalshare.'"/>';
        echo '<input type="hidden" class="dat" value="'.$demater.'"/>';
        echo '<input type="hidden" class="nos" value="'.$numofshare.'"/>';
        echo '<input type="hidden" class="perc" value="'.$percent.'"/>';
        echo '<input type="hidden" class="temp1" value="'.$temp.'"/>';
    }
    public static function increaseDateByDays($days, $date) {
        $calculateddate = strtotime("+".$days."days", strtotime($date));
        $date = date('y-m-d',$calculateddate);
        return $date;
    }
}