<?php

namespace App\Http\Controllers;

use App\Models\UserJourney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserJourneyController extends Controller
{
    public function track(Request $request)
    {
        $requestData = json_decode($request->input('data'), true);
        // dd($requestData);
        if (isset($requestData['purpose'])) {
            $this->decidePurposeWise($requestData['purpose']);
        }

        $journey = new UserJourney();
        $journey->user_id = optional($request->user())->id;
        $journey->user_phone_no = $requestData['user_phone_no'] ?? (optional($request->user())->user_phone_no ?? '');
        $journey->user_account_no = $requestData['user_account_no'] ?? (optional($request->user())->user_account_no ?? '');
        $journey->page = $requestData['page'] ?? '';
        $journey->action = $request->input('action', 'UNDEFINED');
        $journey->data = json_encode($requestData);
        $journey->browser = $request->header('User-Agent');
        $journey->ip_address = $request->ip();
        $journey->save();

        return response()->json(['success' => true]);
    }

    protected function decidePurposeWise($purpose)
    {
        if (strtoupper($purpose) === "CARDACTIVATE") {
            session(['api_calling' => [
                'purpose' => $purpose,
                'api' => 'cardActivate',
                'prompt' => getPromptPath("account-activate-send-otp")
            ]]);
        }

    }

}
