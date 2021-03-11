<?php

namespace App\Console\Commands;

use App\Account;
use App\Test;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;

class Report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:test {desc?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files=$files;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users =[];
        $test_num =[];

        if($this->argument('desc')) {
            $tests = Test::select('test_account_id', DB::raw('count(*) as tests'))->groupBy('test_account_id')->get()->sortByDesc('tests');
        }else{
            $tests = Test::select('test_account_id', DB::raw('count(*) as tests'))->groupBy('test_account_id')->get()->sortBy('tests');
        }

        foreach ($tests as $key => $value) {
            $user = Account::find($value->test_account_id);

            if (is_null($user)) {
                $users[]= 'account was deleted';
            } else {
                $users[]= $user->account_name;
            }

            $test_num[]=$value->tests;
        }

        $body='';
        foreach ($users as $key => $value) {
            $body=$body. "['".$value."','".$test_num[$key]."'],";
        }

        $content= "var dd = {
            content: [

                {
                    style: 'tableExample',
                    table: {
                        body: [
                            [{ text: 'User_name', bold: true },{ text: 'Number of tests', bold: true } ],".$body."                ]
                    }
                },

            ],
            styles: {
                header: {
                    fontSize: 18,
                    bold: true,
                    margin: [0, 0, 0, 10]
                },
                subheader: {
                    fontSize: 16,
                    bold: true,
                    margin: [0, 10, 0, 5]
                },
                tableExample: {
                    margin: [0, 5, 0, 15]
                },
                tableHeader: {
                    bold: true,
                    fontSize: 13,
                    color: 'black'
                }
            },
            defaultStyle: {
                // alignment: 'justify'
            }

        }
        ";

        $file = "report".time().".js";
        $path=base_path();

        $file=$path."/resources/reports/$file";
// dd($file);
            if(!$this->files->put($file, $content))
             return $this->error('Something went wrong!');
            $this->info("report generated!");
    }
    ////
}
