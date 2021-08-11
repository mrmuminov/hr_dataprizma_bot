<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
include "Telegram.php";
include "DB.php";
$telegram = new Telegram("1642425555:AAFX_FCLxTsQV9gGUTihwCOBH5n_WEz6Az8");
$db = new DB();
$buttonMessage = "Anketa to'ldirish";
$telegram->onMessage("/start", function ($telegram, $from) use ($db,$buttonMessage){

    $user = $db->getUser($from->id);
    if (empty($user)) {
        $db->saveUser($from->id, $from->username, 'start', time());
    }
    $db->updateColumn($from->id, 'hr_users','step', "start");
    $telegram->sendMessage([
        'chat_id' => $from->id,
        'text' => "Assalomu alaykum\n \"Dataprizma\" kompaniyasining\nAnketa qabul qilish botiga xush kelibsiz.\n\nAnketa to'ldirish uchun quyidagi tugmadan foydalaning",
        'reply_markup' => json_encode([
            'keyboard' => [
                [['text' => $buttonMessage]],
            ],
            'resize_keyboard' => true,
        ])
    ]);
    die;
});

$user = $db->getUser($telegram->update->message->from->id);
$telegram->onMessage($buttonMessage, function ($telegram, $from) use ($db, $user,$buttonMessage){
    if ($user['step'] == "start") {
        $db->updateColumn($from->id, 'hr_users','step', "anketa_1");
        $telegram->sendMessage([
            'chat_id' => $from->id,
            'text' => "Familiya, ism va sharifingizni kiriting. (Onorov Omadbek Yorqinbekovich)",
            'reply_markup' => json_encode([
                'remove_keyboard' => true,
            ])
        ]);
    }
    die;
});

$telegram->onMessage(-1 , function ($telegram, $from, $text) use ($db, $user,$buttonMessage){

    if (mb_substr($user['step'], 0, 7) == "anketa_") {
        $step = mb_substr($user['step'], 7);
        switch ($step) {
            case "1":
                $db->updateColumn($from->id, 'hr_users','step', "anketa_2");
                $db->saveColumnResume($from->id, 'full_name',$text);
                $db->saveColumnResume($from->id, 'created_at',time());
                $telegram->sendMessage([
                    'chat_id' => $from->id,
                    'text' => "Yashash manzilingizni kiriting. (Andijon viloyati, Asaka shaxri, Mustaqillik 15)",
                    'reply_markup' => null
                ]);
                break;
            case "2":
                $db->updateColumn($from->id, 'hr_users','step', "anketa_3");
                $db->saveColumnResume($from->id, 'region',$text);
                $telegram->sendMessage([
                    'chat_id' => $from->id,
                    'text' => "Qaysi texnologiyalar bilan tanishsiz: (php, java, mysql, yii2, reactjs, ...)",
                    'reply_markup' => null
                ]);
                break;
            case "3":
                $db->updateColumn($from->id, 'hr_users','step', "anketa_4");
                $db->saveColumnResume($from->id, 'tehnology',$text);
                $telegram->sendMessage([
                    'chat_id' => $from->id,
                    'text' => "E-mail kiriting",
                    'reply_markup' => null
                ]);
                break;
            case "4":
                $db->updateColumn($from->id, 'hr_users','step', "anketa_5");
                $db->saveColumnResume($from->id, 'email',$text);
                $telegram->sendMessage([
                    'chat_id' => $from->id,
                    'text' => "Telefon raqamingizni tugmani bosish orqali yuboring",
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [['text' => "Telefon raqam yuborish", 'request_contact' => true]],
                        ],
                        'resize_keyboard' => true,
                    ])
                ]);
                break;
            case "5":
                $phoneUserId = $telegram->update->message->contact->user_id;
                if ($phoneUserId != $from->id) {
                    $telegram->sendMessage([
                        'chat_id' => $from->id,
                        'text' => "O'zingizni telefon raqamingizni yuboring",
                        'reply_markup' => json_encode([
                            'keyboard' => [
                                [['text' => "Telefon raqam yuborish", 'request_contact' => true]],
                            ],
                            'resize_keyboard' => true,
                        ])
                    ]);
                } else {
                    $phone = $telegram->update->message->contact->phone_number;
                    $db->updateColumn($from->id, 'hr_users','step', "anketa_6");
                    $db->saveColumnResume($from->id, 'phone', $phone);
                    $telegram->sendMessage([
                        'chat_id' => $from->id,
                        'text' => "Anketa faylini yuboring. (pdf, doc, docx, jpg, png)",
                        'reply_markup' => json_encode([
                            'remove_keyboard' => true,
                        ])
                    ]);
                }
                break;
            case "6":
                if (empty($telegram->update->message->document)) {
                    $telegram->sendMessage([
                        'chat_id' => $from->id,
                        'text' => "Anketa faylini yuboring. (pdf, doc, docx, jpg, png)",
                        'reply_markup' => json_encode([
                            'remove_keyboard' => true,
                        ])
                    ]);
                } else {
                    $fileId = $telegram->update->message->document->file_id;
                    $fileExtension = explode(".", $telegram->update->message->document->file_name);
                    $fileExtension = $fileExtension[count($fileExtension)-1];
                    if (in_array($fileExtension, ['pdf', 'doc', 'docx', 'jpg', 'png'])) {
                        $db->updateColumn($from->id, 'hr_users', 'step', "anketa_7");
                        $db->saveColumnResume($from->id, 'file', $fileId);
                        $telegram->sendMessage([
                            'chat_id' => $from->id,
                            'text' => "Qo'shimcha ma'lumotlar",
                        ]);
                    } else {
                        $telegram->sendMessage([
                            'chat_id' => $from->id,
                            'text' => "Qo'shimcha ma'lumotlar",
                        ]);
                    }
                }
                break;
            case "7":
                $db->updateColumn($from->id, 'hr_users','step', "start");
                $resume = $db->getResume($from->id);
                $resume['add_info'] = $text;
                $db->saveColumnResume($from->id, 'add_info', $text, 2);
                $telegram->sendMessage([
                    'chat_id' => $from->id,
                    'text' => "Muvofaqiyatli saqlandi. \nTez oraqa mas'ul hodimlarimiz siz bilan bog'lanishadi.",
                ]);
                $telegram->sendMessage([
                    'chat_id' => $from->id,
                    'text' => "Bosh sahifa",
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [['text' => $buttonMessage]],
                        ],
                        'resize_keyboard' => true,
                    ])
                ]);
                $telegram->sendMessage([
                    'chat_id' => '64520993',
                    'text' =>(
                        date("Y.m.d H:i:s", $resume['created_at']) . "\n\n" .
                        "AnketaId: " . $resume['id'] . "\n\n" .
                        "UserId: " . $resume['user_id'] . "\n\n" .
                        "FIO: " . $resume['full_name'] . "\n\n" .
                        "Email: " . $resume['email'] . "\n\n" .
                        "Manzil: " . $resume['region'] . "\n\n" .
                        "Texnologiya: " . $resume['tehnology'] . "\n\n" .
                        "Qo'shimcha ma'lumot: " . $resume['add_info'] . "\n\n" .
                        "Telefon: " . $resume['phone']
                    ),
                ]);
                $telegram->request('sendDocument', [
                    'chat_id' => '64520993',
                    'document' => $resume['file'],
                ]);
                $telegram->sendMessage([
                    'chat_id' => '1053696039',
                    'text' =>(
                        date("Y.m.d H:i:s", $resume['created_at']) . "\n\n" .
                        "AnketaId: " . $resume['id'] . "\n\n" .
                        "UserId: " . $resume['user_id'] . "\n\n" .
                        "FIO: " . $resume['full_name'] . "\n\n" .
                        "Email: " . $resume['email'] . "\n\n" .
                        "Manzil: " . $resume['region'] . "\n\n" .
                        "Texnologiya: " . $resume['tehnology'] . "\n\n" .
                        "Qo'shimcha ma'lumot: " . $resume['add_info'] . "\n\n" .
                        "Telefon: " . $resume['phone']
                    ),
                ]);
                $telegram->request('sendDocument', [
                    'chat_id' => '1053696039',
                    'document' => $resume['file'],
                ]);
                break;
        }
    }
    die;
});
