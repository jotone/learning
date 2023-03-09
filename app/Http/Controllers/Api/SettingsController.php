<?php

namespace App\Http\Controllers\Api;

use App\Classes\FileHelper;
use App\Http\Controllers\BasicApiController;
use App\Models\Settings;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic;
use PHP_ICO;
use SVG\SVG;

class SettingsController extends BasicApiController
{
    /**
     * Update settings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $args = $request->only([
            'fav_icon', //
            'footer_code',//
            'header_code',//
            'login_bg_color',
            'login_btn',
            'login_form_bg_color',
            'login_form_text_color',
            'login_logo_bg_color',
            'logo_img_url',//
            'menu_colors',//
            'override_css',//
            'primary_btn',//
            'site_custom_url',
            'site_title',//
            'site_timezone' //
        ]);

        $data = [];
        $rules = [];

        foreach ($args as $key => $val) {
            switch ($key) {
                case 'fav_icon':
                    // Remove previous favicon
                    FileHelper::recursiveRemove(public_path('/favicon'));
                    // Save favicon file
                    $url = FileHelper::saveFile($val, 'favicon');

                    $mime = $val->getClientMimeType();
                    if (in_array($mime, ['image/svg+xml', 'image/svg', 'image/png'])) {
                        // Five is SVG icon
                        // $rules[$key] = ['required', 'mimes:svg'];
                        // Favicon directory relative path
                        $dir = Str::start('/', pathinfo($url, PATHINFO_DIRNAME));
                        // Icon file path
                        $img_url = $mime === 'image/png' ? $dir . 'icon-192.png' : $dir . 'icon.svg';
                        // Favicon directory full path
                        $full_path = public_path($img_url);
                        // Rename SVG file to icon.svg
                        rename(public_path($url), $full_path);

                        if ($mime === 'image/png') {
                            // Convert PNG icon 512x512 pixels
                            ImageManagerStatic::make(public_path($img_url))
                                ->resize(512, 512, fn($constraint) => $constraint->aspectRatio())
                                ->save(public_path($dir . '/icon-512.png'));
                            // Convert  PNG icon 180x180 pixels
                            ImageManagerStatic::make(public_path($img_url))
                                ->resize(180, 180, fn($constraint) => $constraint->aspectRatio())
                                ->save(public_path($dir . '/apple-touch-icon.png'));
                            // Convert PNG icon 32x32 pixels
                            ImageManagerStatic::make(public_path($img_url))
                                ->resize(32, 32, fn($constraint) => $constraint->aspectRatio())
                                ->save(public_path($dir . '/icon-32.png'));
                            // Convert PNG icon 192x192 pixels
                            ImageManagerStatic::make(public_path($img_url))
                                ->resize(192, 192, fn($constraint) => $constraint->aspectRatio())
                                ->save();
                        } else {
                            // Convert SVG as PNG icon 512x512 pixels
                            $this->svgToPng($full_path, public_path($dir . 'icon-512.png'), [512, 512]);
                            // Convert SVG as PNG icon 192x192 pixels
                            $this->svgToPng($full_path, public_path($dir . 'icon-192.png'), [192, 192]);
                            // Convert SVG as PNG icon 180x180 pixels
                            $this->svgToPng($full_path, public_path($dir . 'apple-touch-icon.png'), [180, 180]);
                            // Convert SVG as PNG icon 32x32 pixels
                            $this->svgToPng($full_path, public_path($dir . 'icon-32.png'), [32, 32]);
                        }
                        // Convert png to ico image
                        $ico = new PHP_ICO(public_path($dir . 'icon-32.png'));
                        // Save ico file
                        $ico->save_ico(public_path($dir . 'favicon.ico'));
                        // Create / Update web manifest file
                        $this->saveWebManifest($dir);
                    } else {
                        // File is ico
                        // $rules[$key] = ['required', 'mimes:ico'];
                        $img_url = $url;
                    }

                    $data[$key] = $img_url;
                    break;
                case 'footer_code':
                case 'header_code':
                    $rules[$key] = ['nullable'];
                    $data[$key] = $val;
                    break;
                case 'logo_img_url':
                    // Check file mimetype
                    if (in_array($val->getClientMimeType(), ['image/png', 'image/jpeg'])) {
                        $rules[$key] = ['required', 'image'];
                        // Save file
                        $url = FileHelper::saveFile($val, 'images');

                        $path_info = pathinfo($url);
                        // Rebuild image url
                        $img_url = sprintf('%s/%s.%s', $path_info['dirname'], 'logo', $path_info['extension']);
                        // Rename file
                        rename(public_path($url), public_path($img_url));

                        $data[$key] = $img_url;
                    }
                    break;
                case 'login_btn':
                case 'menu_colors':
                case 'primary_btn':
                    $rules[$key] = ['array'];
                    $rules[$key . '*'] = ['array'];
                    $data[$key] = $val;
                    break;
                case 'override_css':
                    $rules[$key] = ['nullable', 'string'];
                    break;
                case 'site_custom_url':
                    // TODO send request to admin.copemember
                    break;
                case 'login_bg_color':
                case 'login_form_bg_color':
                case 'login_form_text_color':
                case 'login_logo_bg_color':
                case 'site_timezone':
                case 'site_title':
                    $rules[$key] = ['required', 'string', 'max:255'];
                    $data[$key] = $val;
                    break;
            }
        }

        // Run request validation
        $validation = Validator::make($args, $rules);

        if ($validation->fails()) {
            if (isset($args['logo_img_url'])) {
                FileHelper::recursiveRemove(public_path($data['logo_img_url']));
            }
            if (isset($args['fav_icon'])) {
                FileHelper::recursiveRemove(public_path('/favicon'));
            }

            return response()->json(['errors' => $validation->errors()], 422);
        }
        // Update settings values
        foreach ($data as $key => $val) {
            $model = Settings::firstWhere('key', $key);
            if (empty($model)) dd($key, $val);
            $model->value = $val;
            $model->save();
        }

        if (isset($args['override_css'])) {
            file_put_contents(public_path('/css/override.css'), $args['override_css']);
        }

        return response()->json(Settings::whereIn('key', array_keys($args))->get());
    }

    /**
     * Create or update web manifest file
     *
     * @param string $dir
     * @return void
     */
    protected function saveWebManifest(string $dir): void
    {
        file_put_contents(public_path($dir . '/manifest.webmanifest'), json_encode([
            'icons' => [
                [
                    'src'   => $dir . '/icon-32.png',
                    'type'  => 'image/png',
                    'sizes' => '32x32'
                ],
                [
                    'src'   => $dir . '/icon-192.png',
                    'type'  => 'image/png',
                    'sizes' => '192x192'
                ],
                [
                    'src'   => $dir . '/icon-512.png',
                    'type'  => 'image/png',
                    'sizes' => '512x512'
                ]
            ],
        ]));
    }

    /**
     * Convert svg to png
     *
     * @param string $from
     * @param string $to
     * @param array $dimensions
     * @return string
     */
    public function svgToPng(string $from, string $to, array $dimensions): string
    {
        $svg = SVG::fromFile($from);
        $raster_image = $svg->toRasterImage($dimensions[0], $dimensions[1]);
        imagepng($raster_image, $to);
        return $to;
    }
}