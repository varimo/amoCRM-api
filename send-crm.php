<?php

    ini_set( 'display_errors', '1' );

    use AmoCRM\{AmoAPI, AmoContact, AmoLead, AmoObject, AmoNote, AmoTask, AmoAPIException};
    use AmoCRM\TokenStorage\{FileStorage, TokenStorageException};

    require __DIR__.'/vendor/autoload.php';

    $back = "<p><a href=\"javascript: history.back()\">Вернуться назад</a></p>";
    
    
        if (!empty($_POST["name"]) and !empty($_POST["phone"]) and !empty($_POST["email"]) and !empty($_POST["city"]) and !empty($_POST["service"]) and !empty($_POST["message"])) {
            $name = $_POST["name"];
            $phone = $_POST["phone"];
            $email = $_POST["email"];
            $city = $_POST["city"];
            $service = $_POST["service"];
            $message = $_POST["message"];
            $time1 = time() + 300;
            $time2 = time() + 900;
            $price;
            if ($service == 'Диагностика') {
                $price = 100;
            }
            if ($service == 'Ремонт') {
                $price = 500;
            }
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (!empty($GLOBALS['alert'])) {
                    echo "Данные из формы не отправлены. Обнаружены ошибки.";
                }
                else {
                    try {
                        // Параметры авторизации по протоколу oAuth 2.0
                        $clientId     = '1a24a5e8-8d13-49aa-a8ad-a17f9bc13a0f';
                        $clientSecret = '74JprdjsFgQcrDVm2TpSt2Mk0kKyfYiusNHn5nUNWb7zYbrhSFHIn3FTMkFrhwTQ';
                        $authCode     = 'def502006dfb3917ea34ef35c3beb34f2fd98d96bedb96fb0356e29543ae65832658f70949e5b95f65e9b97877105fa891c23c66d22396aa02debee07e87b404482169143f9a615fe5096c24af5b396633959cf0630a3369391177125a76c8b80c0347217a431698eb3f6d79d31a77885da48065ccb5c0a7b8cf300e9d545fe96fe1de56fe7ef0d260a70ce7103face56842f6919061acb85aa3dddbe4fc4a3d57d3579d29724fd8ff938546d933da7774e3ce1e86393d280f70b83400d4920dce1aa1605a919177f865f537dd016f9c17b6c5b24c78904f50945a087ce85f3e4b5260d7458194fb88e0c7dd9374a0a1beec185f3917c1ef743495627966599e80cbe7b39d1f5e8b086a744e72fd5ca03beb6174cd594dca12b1b97a9a6a54ba85a44c52b6be7270f4e47e2187f07bd45185ec0959913e1e623be0b5e4b7d691e7f0b32bd864c59626ed9fced5ddaf1cfb331dec93e899ee40ab2fe9ca2368876a5793a97a6c1113ea84050eb4c43d8cb9fce3112759525f0537ea4f469889b58e22b61a72fc0621b292d864a340c3400416c33a706d39dee6a8eb396e4b0bcc3450ad116775cd258b64eedc5141d0f57d2b3578de19059909bf5f43';
                        $redirectUri  = 'http://varimo.ru/';
                        $subdomain    = 'shid98';

                        $domain = AmoAPI::getAmoDomain($subdomain);
                        $isFirstAuth = !(new FileStorage())->hasTokens($domain);

                        if ($isFirstAuth) {
                            // Первичная авторизация
                            AmoAPI::oAuth2($subdomain, $clientId, $clientSecret, $redirectUri, $authCode);
                        } else {
                            // Последующие авторизации
                            AmoAPI::oAuth2($subdomain);
                        }

                        $itemsPhone = AmoAPI::getContacts([
                            'query' => $phone                            
                        ]);
                        $itemsEmail = AmoAPI::getContacts([
                            'query' => $email                            
                        ]);
                        if (is_null($itemsPhone) && is_null($itemsEmail)) {
                            $contact1 = new AmoContact([
                                'name'                => $name,
                                'responsible_user_id' => 29683153
                                ]);
                                $contact1->setCustomFields([
                                    '1076719' => $city,
                                    '1076523' => [[
                                        'value' => $phone,
                                        'enum'  => 'WORK'
                                    ]],
                                    '1076525' => [[
                                        'value' => $email,
                                        'enum'  => 'WORK'
                                    ]]
                                ]);                        
                                $contactId = $contact1->save();
    
                                // Создание новой сделки
                                $lead1 = new AmoLead([
                                    'name'                => $service,
                                    'responsible_user_id' => 29683153,
                                    'sale'                => $price
                                ]);
                                $lead1->setCustomFields([
                                    '1076717' => [[
                                        'value' => $service
                                    ]]
                                ]);
                                $lead1->addContacts($contactId);
                                $leadId = $lead1->save();
    
                                // Создание примечания
                                $note = new AmoNote([
                                    'element_id'   => $leadId,
                                    'note_type'    => AmoNote::COMMON_NOTETYPE,
                                    'element_type' => AmoNOTE::LEAD_TYPE,
                                    'text'         => $message,
                                    'responsible_user_id' => 29683153
                                ]);
                                $noteId = $note->save();
    
                                // Создание задачи
                                $task = new AmoTask([
                                    'task_type'        => AmoTASK::CALL_TASKTYPE,
                                    'element_type'     => AmoTask::LEAD_TYPE,
                                    'element_id'       => $leadId,
                                    'text'             => 'Обработать заявку',
                                    'complete_till_at' => $time1,
                                    'responsible_user_id' => 29683153
                                ]);
                                $taskId = $task->save();
                        }
                        else {
                            $task = new AmoTask([
                                'task_type'        => AmoTASK::CALL_TASKTYPE,
                                'element_type'     => AmoTask::LEAD_TYPE,
                                'text'             => 'Повторная заявка',
                                'complete_till_at' => $time2,
                                'responsible_user_id' => 29683153
                            ]);
                            $taskId = $task->save();
                        }
                       
                    } catch (AmoAPIException $e) {
                        printf('Ошибка авторизации (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
                    } catch (TokenStorageException $e) {
                        printf('Ошибка обработки токенов (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
                    }
                    
                }
            }
            else {
                echo "Некорректные данные! $back";
            }
        }  
        else {
            echo "Для отправки сообщения заполните все поля! $back";
        }      
?>