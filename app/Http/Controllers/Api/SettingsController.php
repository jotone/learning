<?php

namespace App\Http\Controllers\Api;

use App\Classes\FileHelper;
use App\Http\Controllers\BasicApiController;
use App\Mail\TestSMTP;
use App\Models\Settings;
use App\Traits\{ConfigTrait, SettingsTrait};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\{Facades\Mail, Facades\Validator, Str};
use Intervention\Image\ImageManagerStatic;
use PHP_ICO;
use SVG\SVG;

class SettingsController extends BasicApiController
{
    use ConfigTrait, SettingsTrait;

    /**
     * List of available to update settings
     * @var array|string[]
     */
    protected array $settings_list = [
        'fav_icon',
        'footer_code',
        'footer_color',
        'header_code',
        'legal_address',
        'login_bg_color',
        'login_bg_img',
        'login_btn',
        'login_form_bg_color',
        'login_form_text_color',
        'login_logo_bg_color',
        'logo_img',
        'main_language',
        'menu_colors',
        'override_css',
        'primary_btn',
        'privacy_policy',
        'site_custom_url',//
        'site_title',
        'site_timezone',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'smtp_host',
        'smtp_password',
        'smtp_port',
        'smtp_username',
        'terms_of_service'
    ];

    /**
     * Update settings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        // Get request data
        $args = $request->only($this->settings_list);
        // Convert request data and extract $rules and $data variables
        extract($this->treatRequestData($args));
        // Run request validation
        $validation = Validator::make($args, $rules);
        if ($validation->fails()) {
            $this->removeSettingsImages($args, $data);

            return response()->json(['errors' => $validation->errors()], 422);
        }

        // Update settings values
        foreach ($data as $key => $val) {
            $model = Settings::firstWhere('key', $key);
            if (empty($model)) dd($key, $val);
            $model->value = $val;
            $model->save();
        }

        // Generate override css file
        if (isset($args['override_css'])) {
            file_put_contents(public_path('/css/override.css'), $args['override_css']);
        }

        // Generate login.css file
        if (
            !array_diff(
                ['smtp_encryption', 'smtp_host', 'smtp_password', 'smtp_port', 'smtp_username'],
                array_keys($args)
            )
        ) {
            $this->testSMTP($args);
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
                    'src' => $dir . '/icon-32.png',
                    'type' => 'image/png',
                    'sizes' => '32x32'
                ],
                [
                    'src' => $dir . '/icon-192.png',
                    'type' => 'image/png',
                    'sizes' => '192x192'
                ],
                [
                    'src' => $dir . '/icon-512.png',
                    'type' => 'image/png',
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
    protected function svgToPng(string $from, string $to, array $dimensions): string
    {
        $svg = SVG::fromFile($from);
        $raster_image = $svg->toRasterImage($dimensions[0], $dimensions[1]);
        imagepng($raster_image, $to);
        return $to;
    }

    /**
     * Remove favicon images, login background image and logo image
     *
     * @param array $args
     * @param array $data
     * @return void
     */
    protected function removeSettingsImages(array $args, array $data): void
    {
        if (isset($args['fav_icon'])) {
            FileHelper::recursiveRemove(public_path('/favicon'));
        }
        if (isset($args['login_bg_img'])) {
            FileHelper::recursiveRemove(public_path($data['login_bg_img']));
        }
        if (isset($args['logo_img'])) {
            FileHelper::recursiveRemove(public_path($data['logo_img']));
        }
    }

    /**
     * Send test email
     * @param array $args
     * @return void
     */
    protected function testSMTP(array $args): void
    {
        $this->setEmailConfig($args);

        Mail::to(config('mail.from.address'))->send(new TestSMTP());
    }

    /**
     * Create validation rules and convert request data
     *
     * @param array $args
     * @return array[]
     */
    protected function treatRequestData(array $args): array
    {
        $result = [
            'data' => [],
            'rules' => []
        ];
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
                        // $result['rules'][$key] = ['required', 'mimes:svg'];
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
                        // $result['rules'][$key] = ['required', 'mimes:ico'];
                        $img_url = $url;
                    }

                    $result['data'][$key] = $img_url;
                    break;
                case 'footer_code':
                case 'header_code':
                case 'legal_address':
                case 'privacy_policy':
                case 'smtp_from_name':
                case 'terms_of_service':
                    $result['rules'] = ['nullable'];
                    $result['data'][$key] = empty($val) || is_string($val) || is_int($val) ? $val : json_encode($val);
                    break;
                case 'login_bg_img':
                case 'logo_img':
                    // Check file mimetype
                    if (in_array($val->getClientMimeType(), ['image/png', 'image/jpeg'])) {
                        //$result['rules'[[$key] = ['required', 'image'];
                        // Save file
                        $url = FileHelper::saveFile($val, 'images');

                        $path_info = pathinfo($url);
                        // Rebuild image url
                        $filename = ($key == 'login_bg_img' ? 'login-bg' : 'logo') . '.' . $path_info['extension'];
                        $img_url = Str::finish($path_info['dirname'], '/') . $filename;
                        // Rename file
                        rename(public_path($url), public_path($img_url));

                        $image_processing = Settings::firstWhere('key', $key . '_processing');

                        if (!empty($image_processing)) {
                            $dimensions = (array)$image_processing->val;
                            if (count($dimensions) >= 2) {
                                ImageManagerStatic::make(public_path($img_url))
                                    ->resize($dimensions[0], $dimensions[1], fn($constraint) => $constraint->aspectRatio())
                                    ->save();
                            }
                        }

                        $result['data'][$key] = $img_url;
                    }
                    break;
                case 'login_btn':
                case 'menu_colors':
                case 'primary_btn':
                    $result['rules'][$key] = ['array'];
                    $result['rules'][$key . '*'] = ['array'];
                    $result['data'][$key] = $val;
                    break;
                case 'override_css':
                    $result['rules'][$key] = ['nullable', 'string'];
                    break;
                case 'site_custom_url':
                    // TODO send request to admin.copemember
                    break;
                case 'footer_color':
                case 'login_bg_color':
                case 'login_form_bg_color':
                case 'login_form_text_color':
                case 'login_logo_bg_color':
                case 'main_language':
                case 'site_timezone':
                case 'site_title':
                case 'smtp_encryption':
                case 'smtp_host':
                case 'smtp_password':
                case 'smtp_port':
                case 'smtp_username':
                    $result['rules'][$key] = ['required', 'string', 'max:255'];
                    $result['data'][$key] = $val;
                    break;
                case 'smtp_from_address':
                    $result['rules'][$key] = ['required', 'email'];
                    $result['data'][$key] = $val;
                    break;
            }
        }
        return $result;
    }
}