<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand;

class customServe extends ServeCommand
{
    protected $signature = 'serve:custom';

    protected $description = 'Start the Laravel development server with a custom configuration';

    protected $host = '127.0.0.1'; // Your custom host IP address
    protected $port = '8000';  

    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = '8000';
        parent::__construct();
    }

    // public function handle()
    // {
    // Your custom port number

    //     $this->call('serve', [
    //         '--host' => $host,
    //         '--port' => $port,
    //     ]);

    //     $this->info("Laravel development server started on http://$host:$port");
    // }
    protected function handleProcessOutput()
    {
        return fn ($type, $buffer) => str($buffer)->explode("\n")->each(function ($line) {
            if (str($line)->contains('Development Server (http')) {
                if ($this->serverRunningHasBeenDisplayed) {
                    return;
                }

                $this->components->info("Server running on [http://{$this->host}:{$this->port}].");
                $this->comment('  <fg=yellow;options=bold>Press Ctrl+C to stop the server</>');

                $this->newLine();

                $this->serverRunningHasBeenDisplayed = true;
            } elseif (str($line)->contains(' Accepted')) {
                $requestPort = $this->getRequestPortFromLine($line);

                $this->requestsPool[$requestPort] = [
                    $this->getDateFromLine($line),
                    false,
                ];
            } elseif (str($line)->contains([' [200]: GET '])) {
                $requestPort = $this->getRequestPortFromLine($line);

                $this->requestsPool[$requestPort][1] = trim(explode('[200]: GET', $line)[1]);
            } elseif (str($line)->contains(' Closing')) {
                $requestPort = $this->getRequestPortFromLine($line);

                if (empty($this->requestsPool[$requestPort])) {
                    return;
                }

                [$startDate, $file] = $this->requestsPool[$requestPort];

                $formattedStartedAt = $startDate->format('Y-m-d H:i:s');

                unset($this->requestsPool[$requestPort]);

                [$date, $time] = explode(' ', $formattedStartedAt);

                $this->output->write("  <fg=gray>$date</> $time");

                $runTime = $this->getDateFromLine($line)->diffInSeconds($startDate);

                if ($file) {
                    $this->output->write($file = " $file");
                }

                $dots = max(terminal()->width() - mb_strlen($formattedStartedAt) - mb_strlen($file) - mb_strlen($runTime) - 9, 0);

                $this->output->write(' '.str_repeat('<fg=red>.</>', $dots));
                $this->output->writeln(" <fg=gray>~ {$runTime}s</>");
            } elseif (str($line)->contains(['Closed without sending a request'])) {
                // ...
            } elseif (! empty($line)) {
                $warning = explode('] ', $line);
                $this->components->warn(count($warning) > 1 ? $warning[1] : $warning[0]);
            }
        });
    }
}
