<?php

namespace minejufe\cas\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Facades\Option;
use App\Services\OptionForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Events\Dispatcher;
use App\Events;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RedirectController extends Controller
{
    public function Login(){
        return redirect('https://ssl.jxufe.edu.cn/cas/login',301);
    }
}




