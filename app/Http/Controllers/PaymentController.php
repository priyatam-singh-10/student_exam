<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Submission;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;

class PaymentController extends Controller
{

public function initiate(Request $request)
{
    $user = Auth::guard('api')->user();

    $validator = Validator::make($request->all(), [
        'submission_id' => 'required|integer|exists:submissions,id',
        'amount' => 'required|integer|min:100',
        'currency' => 'sometimes|string|size:3',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $submission = Submission::where('user_id', $user->id)
        ->findOrFail($request->submission_id);

    $payment = new Payment();
    $payment->user_id = $user->id;
    $payment->submission_id = $submission->id;
    $payment->provider = 'razorpay';
    $payment->amount = $request->amount;
    $payment->currency = strtoupper($request->currency ?? 'INR');
    $payment->status = 'pending';
    $payment->meta = [];
    $payment->save();

    // Razorpay only
    $keyId = env('RAZORPAY_KEY_ID');
    $keySecret = env('RAZORPAY_KEY_SECRET');

    if (empty($keyId) || empty($keySecret)) {
        return response()->json([
            'success' => false,
            'message' => 'Razorpay keys missing. Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET in .env and run php artisan config:clear.',
        ], 500);
    }

    try {
        $api = new Api($keyId, $keySecret);
        $order = $api->order->create([
            'receipt' => 'order_rcptid_' . $payment->id,
            'amount' => $payment->amount * 100, 
            'currency' => $payment->currency,
            'notes' => [
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'submission_id' => $submission->id,
            ],
        ]);

        $payment->meta = ['razorpay_order_id' => $order['id']];
        $payment->provider_payment_id = $order['id'] ?? null;
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'Razorpay order created successfully',
            'order' => [
                'id' => $order['id'],
                'amount' => $order['amount'] / 100, 
                'currency' => $order['currency'],
                'status' => $order['status'],
                'receipt' => $order['receipt'],
                'notes' => $order['notes'],
            ],
            'key_id' => $keyId,
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => 'false',
            'message' => 'Failed to create Razorpay order: ' . $e->getMessage(),
        ], 500);
    }
}

public function stripeWebhook(Request $request)
{
    $providerPaymentId = $request->input('data.object.id');
    $paymentId = $request->input('data.object.metadata.payment_id');
    return $this->markSuccessAndGenerateReceipt((int)$paymentId, 'stripe', $providerPaymentId);
}

public function razorpayWebhook(Request $request)
{
    $providerPaymentId = $request->input('payload.payment.entity.id');
    $paymentId = $request->input('payload.payment.entity.notes.payment_id');
    return $this->markSuccessAndGenerateReceipt((int)$paymentId, 'razorpay', $providerPaymentId);
}

public function receipt(Request $request, $paymentId)
{

    $user = Auth::guard('api')->user();
    $payment = Payment::where('user_id', $user->id)->find($paymentId)->first();
    if (!$payment) {
     return response()->json([
            'success' => 'false',
            'message' => 'Payment not found'
        ], 404);
    }
    if (!$payment->receipt_path || !Storage::disk('local')->exists($payment->receipt_path)) {
        abort(404, 'Receipt not available');
    }
    return response()->file(Storage::path($payment->receipt_path));
}

protected function markSuccessAndGenerateReceipt(int $paymentId, string $provider, ?string $providerPaymentId)
{
    $payment = Payment::with(['user', 'submission.form'])->findOrFail($paymentId);
    $payment->update([
        'status' => 'succeeded',
        'provider_payment_id' => $providerPaymentId,
    ]);

    $pdfService = app(PdfService::class);
    $receiptNumber = 'RCP-'.date('Ymd').'-'.$payment->id;
    $path = 'receipts/'.$receiptNumber.'.pdf';
    $data = [
        'receipt_number' => $receiptNumber,
        'paid_at' => now()->toDateTimeString(),
        'user_name' => $payment->user->name,
        'user_email' => $payment->user->email,
        'form_title' => optional($payment->submission->form)->title,
        'submission_id' => $payment->submission_id,
        'provider' => $provider,
        'currency' => $payment->currency,
        'amount' => $payment->amount,
        'status' => $payment->status,
    ];
    $storedPath = $pdfService->generateReceipt($data, $path);
    $payment->update(['receipt_path' => $storedPath]);

    return response()->json(['message' => 'Payment succeeded', 'payment' => $payment]);
}

public function sampleReceipt(Request $request)
{
    $user = $request->user();
    $receiptNumber = 'SAMPLE-'.date('Ymd-His');
    $path = 'receipts/'.$receiptNumber.'.pdf';
    $userPayment  = Payment::where('user_id', $request->user()->id)->first();

    $data = [
        'receipt_number' => $receiptNumber,
        'paid_at' => now()->toDateTimeString(),
        'user_name' => $user->name,
        'user_email' => $user->email,
        'form_title' => 'Sample Exam Form',
        'submission_id' => 0,
        'provider' => 'razorpay',
        'currency' => 'INR',
        'amount' => $userPayment->amount ?? 0,
        'status' => 'succeeded',
    ];
    $pdfService = app(\App\Services\PdfService::class);
    $storedPath = $pdfService->generateReceipt($data, $path);
    return response()->file(\Illuminate\Support\Facades\Storage::path($storedPath));
}

}

