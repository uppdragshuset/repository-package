<?php

namespace Uppdragshuset\AO\Repository\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new repository';

    protected $files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = ucwords($this->argument('model'));

        file_exists(app_path('Repositories')) ? '' : mkdir(app_path('Repositories'));
        file_exists(app_path('Presenters')) ? '' : mkdir(app_path('Presenters'));
        file_exists(app_path('Transformers')) ? '' : mkdir(app_path('Transformers'));

        $repositoryStub = $this->files->get(app_path('Stubs/Repository.stub'));
        $repositoryStub = str_replace('{{Model}}', $model, $repositoryStub);
        $this->files->put(app_path('Repositories/' . $model . 'Repository.php'), $repositoryStub);

        $eloquentRepositoryStub = $this->files->get(app_path('Stubs/EloquentRepository.stub'));
        $eloquentRepositoryStub = str_replace('{{Model}}', $model, $eloquentRepositoryStub);
        $this->files->put(app_path('Repositories/Eloquent' . $model . 'Repository.php'), $eloquentRepositoryStub);

        $presenterStub = $this->files->get(app_path('Stubs/Presenter.stub'));
        $presenterStub = str_replace('{{Model}}', $model, $presenterStub);
        $this->files->put(app_path('Presenters/' . $model . 'Presenter.php'), $presenterStub);

        $transformerStub = $this->files->get(app_path('Stubs/Transformer.stub'));
        $transformerStub = str_replace('{{Model}}', $model, $transformerStub);
        $this->files->put(app_path('Transformers/' . $model . 'Transformer.php'), $transformerStub);
    }
}
