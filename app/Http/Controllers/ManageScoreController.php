<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManageScoreController extends Controller
{
    public function index(){
        return view('guru.manage-scores.index');
    }
}
