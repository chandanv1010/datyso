<?php  
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Classes\System as SystemConfig;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller {

    protected $systemLibrary;
    protected $systemService;
    protected $systemRepository;
    protected $language;

    public function __construct(
        SystemConfig $systemLibrary,

    ){
        
        $this->systemLibrary = $systemLibrary;
    }

    public function index(){
        
        $systemConfig = $this->systemLibrary->config();
        $systemDatabases = System::get();
        $systems = convert_array($systemDatabases, 'keyword', 'content');
        
        $config['seo'] = __('messages.system');
        return view('admin.system.index', compact(
            'systemConfig',
            'systems',
        ));
    }

    public function store(Request $request){
        DB::beginTransaction();
        try{
            $config = $request->input('config');
            $payload = [];
            if(count($config)){
                foreach($config as $key => $val){
                    $payload = [
                        'keyword' => $key,
                        'content' => $val,
                    ];
                    $condition = ['keyword' => $key];
                    System::updateOrInsert($payload, $condition);
                }
            }
            DB::commit();
            return redirect()->back();
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            // return false;
        }
    }
}