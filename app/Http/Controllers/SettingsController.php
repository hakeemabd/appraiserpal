<?php
namespace App\Http\Controllers;

use App\Repositories\SettingRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SettingsController extends Controller
{
    private $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $setting = $this->settingRepository->create($request->only('user_id', 'name'));
        return response()->json($setting);
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {   
        try {
            //dd($request->only('setting_value')); die();
            $settings = $request->setting_value;
            //dd($settings);
            $userId = Sentinel::check()->id;
            foreach ($settings as $key => $value) {
                //dd($key);
                $setting = $this->settingRepository->getByKey($key);
                //dd($setting);
                $setting->user_id = $userId;
                $setting->value = $value;
                $setting->save();
            }

            return response()->json([
                'success' => true
            ], SymfonyResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error, try again later',
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function destroy($id, Request $request)
    {
        $setting = $this->settingRepository->find($id);
        if ($setting->user_id != $request->user_id) {
            throw new AccessDeniedHttpException();
        }
        $this->settingRepository->delete($id);
        return response()->json();
    }

    public function index()
    {
        $settings = $this->settingRepository->getSettings();
        return view('settings.index', [
            'settings' => $settings,
        ]);
    }
}