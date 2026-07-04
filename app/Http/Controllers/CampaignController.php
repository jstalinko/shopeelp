<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Helper;
class CampaignController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $slug = $request->slug;
        $linkModel = Link::where('slug',$slug)->first();
        if(!$linkModel){
            abort(404);
        }

        if(!$linkModel->active){
            abort(404);
        }
        
        
        $geo = Helper::geolocation(Helper::realIP());
        $countryName = $geo['country'] ?? 'unknwn';
        $countryCode = $geo['countryCode'] ?? 'UNKNWN';
        $isp = $geo['isp'] ?? '--';
        $proxy = $geo['proxy'] ?? false;

        //---------- collect link configuration //
        $target_url = Helper::buildTargetUrl($linkModel->target_urls);
        $lock_country = Helper::comma2array($linkModel->lock_country);
        $lock_device = Helper::comma2array($linkModel->lock_device);
        $lock_browser = Helper::comma2array($linkModel->lock_browser);
        $campaign_method = $linkModel->campaign_method; 
        $template = Template::where('template_path' ,'templates/'.$linkModel->template)->first();



        if($campaign_method == 'redirect')
        {
            header('HTTP/1.1 301 Moved Permanently');
            header('location: '.$target_url);
            die();
        }
        else{

        if(file_exists(resource_path('views/'.$template->template_path.'.blade.php'))){

            $template_config = json_decode($template->config,true);
            return view(str_replace('/','.',$template->template_path),[
                'redirect_url' => $target_url,
                ...$template_config
            ]);
        }else{
            die('Tema not exist');
        }

    }
    }
}
