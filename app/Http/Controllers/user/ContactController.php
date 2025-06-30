<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactInquiry;
use App\Mail\ContactInquiryConfirmation;
use Illuminate\Support\Facades\Cache;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // ✅ レート制限：10分以内の再送信をブロック
        $lastSent = Cache::get("contact_last_sent_$userId");
        if ($lastSent && now()->diffInSeconds($lastSent) < 60) {
            return response()->json([
                'success' => false,
                'message' => '60秒以内に再送信することはできません。',
            ], 429);
        }

        // ✅ バリデーション
        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ], [
            'subject.required' => 'お問い合わせ内容を入力してください。',
            'subject.max' => 'お問い合わせ内容が長すぎます（100文字以内）',
            'message.required' => '本文を入力してください。',
            'message.max' => '本文が長すぎます（1000文字以内）',
        ]);

        // ✅ レート通過 → 現在時刻を保存（10分間キャッシュ）
        Cache::put("contact_last_sent_$userId", now(), 600);

        try {
            // ✅ 管理者へメール送信
            Mail::to(config('mail.notification_address'))->send(new ContactInquiry([
                'user_name' => $user->name,
                'email' => $user->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]));

            // ✅ ユーザーへ自動返信メール送信
            Mail::to($user->email)->send(new ContactInquiryConfirmation([
                'user_name' => $user->name,
                'subject' => $request->subject,
                'message' => $request->message,
            ]));

            return response()->json([
                'success' => true,
                'message' => 'お問い合わせを送信しました。',
            ]);
        } catch (\Exception $e) {
            Log::error('お問い合わせメール送信エラー', [
                'user_id' => $userId,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'お問い合わせの送信中にエラーが発生しました。',
            ], 500);
        }
    }
}
