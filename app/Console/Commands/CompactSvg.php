<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompactSvg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'svg {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        $path = resource_path('css/icons');

        $filepath = $path . '/' . $file . '.svg';
        if (!file_exists($filepath)) {
            dd('File not exists.');
        }

        $old_content = file_get_contents($filepath);
        $content = explode("\n", $old_content);

        $svg = '';
        foreach ($content as $str) {
            $str = trim($str);
            if (str_starts_with($str, '<svg')) {
                preg_match('/<(\w+)(.*?)>/', $str, $matches);

                preg_match_all('/(\w+)="([^"]*)"/', $matches[2], $attributeMatches, PREG_SET_ORDER);

                $attributes = [];
                foreach ($attributeMatches as $match) {
                    $attributes[$match[1]] = $match[2];
                }
                $svg = '<svg viewBox="' . $attributes['viewBox'] . '" xmlns="' . $attributes['xmlns'] . '">';
            } elseif (str_contains($str, '<path')) {
                $a = preg_match('/d="([^"]+)"/', $str, $matches);
                $svg_path = explode(' ', $matches[1]);

                $result = '';
                foreach ($svg_path as $value) {
                    $parts = preg_split('/(?<=[a-zA-Z])(?=[0-9])|(?<=[0-9])(?=[a-zA-Z])/', $value);
                    foreach ($parts as $part) {
                        $result .= is_numeric($part) ? (int)round($part) : $part;
                    }
                    $result .= ' ';
                }
                $svg .= '<path d="' . trim($result) . '"/>';
            } else {
                $svg .= $str;
            }
        }

        $this->info($svg);
        $old = strlen($old_content);
        $this->info('<fg=white>Old content length: </><fg=red>' . $old . '</>');

        $new = strlen($svg);

        $this->info(
            '<fg=white>Processed content length: </><fg=' . ($new < $old ? 'green' : 'red') . '>' . $new . '</>'
        );

        if ($this->confirm('Do you want to rewrite the file?')) {
            file_put_contents($filepath, $svg);
        }
        return 0;
    }
}
