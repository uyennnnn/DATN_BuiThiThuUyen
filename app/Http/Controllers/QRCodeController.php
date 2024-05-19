<?php

namespace App\Http\Controllers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class QRCodeController extends Controller
{
    public function index()
    {
        $expired = getExpiredBySettingNightStamp();
        $url = URL::temporarySignedRoute(
            'user.attendance', $expired
        );
        $encodedUrl = urlencode($url);
        $imageUrl = "/qrcode/image?url=$encodedUrl";

        return Inertia::render('Admin/QRCode/Index', [
            'imageUrl' => $imageUrl,
        ]);
    }

    public function image(Request $request)
    {

        $url = $request->input('url');
        $result = Builder::create()
            ->writer(new PngWriter())
            // ->writerOptions([])
            ->data($url)
            // ->encoding(new Encoding('UTF-8'))
            // ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            // ->size(300)
            // ->margin(10)
            // ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            // // ->logoPath(__DIR__ . '/assets/symfony.png')
            // ->logoResizeToWidth(50)
            // ->logoPunchoutBackground(true)
            // ->labelText('This is the label')
            // ->labelFont(new NotoSans(20))
            // ->labelAlignment(LabelAlignment::Center)
            // ->validateResult(false)
            ->build();
        header('Content-Type: '.$result->getMimeType());
        echo $result->getString();
    }
}
