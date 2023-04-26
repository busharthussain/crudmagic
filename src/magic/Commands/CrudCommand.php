<?php

namespace bushart\crudmagic\magic\Commands;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CrudCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magic:resource {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new resource';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return;
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . 'Models\\' . $model;
        }

        return $model;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $this->handleCommands();
        $this->info($this->type . ' created successfully.');
        $this->info('Do not forget to register any bindings.');
    }

    protected function handleCommands()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);

        //Check if the model exists
        $modelClass = $this->parseModel($this->getNameInput());
        $string = str_replace($this->getNamespace($name) . '\\', '', $name);
        $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
        $tableName = Str::plural(str_replace(' ', '_', $tableName));
        if (!class_exists($modelClass)) {
            if ($this->confirm("The {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:migration', ['name' => 'create_'.$tableName.'_table','--create' => $tableName]);
                $this->call('magic:model', ['name' => $modelClass]);
            }
        }

        //Generate service
        if ($this->confirm("Would you like to generate the service class?", true)) {
            $this->call('magic:service', ['name' => $this->getNameInput() . 'Service']);
        }

        //Generate controller
        if ($this->confirm("Would you like to generate a resource controller?", true)) {
            $controller_name = $this->getNameInput() . 'Controller';
            $this->call('magic:controller', ['name' => $controller_name]);
        }



        //Generate Views
        if ($this->confirm('Would you like to generate the views?', true)) {
            $this->call('magic:views', ['name' => $this->getNameInput()]);
        }

        if ($this->confirm('Would you like to generate the route?', true)) {
            $this->call('magic:route', ['name' => $this->getNameInput()]);
        }
    }
}
