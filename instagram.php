<?php
require __DIR__ . '/vendor/autoload.php';
$ig = new \InstagramAPI\Instagram();
$climate = new \League\CLImate\CLImate();
$climate->green()->bold(
    "
                                          _                ___           
  /\  /\_   _ _ __   ___ _ ____   _____ | |_ ___ _ __    / _ \_ __ ___  
 / /_/ / | | | '_ \ / _ \ '__\ \ / / _ \| __/ _ \ '__|  / /_)/ '__/ _ \ 
/ __  /| |_| | |_) |  __/ |   \ V / (_) | ||  __/ |    / ___/| | | (_) |
\/ /_/  \__, | .__/ \___|_|    \_/ \___/ \__\___|_|    \/    |_|  \___/ 
        |___/|_|                                                      "
);
$climate->out('');
$climate->green()->bold('Hypervoter Pro Terminal - OpenSource');
$climate->green()->bold('v1.9');
$climate->out('');
$climate->green('© Developed by HyperVoter (https://hypervoter.com) - OpenSource by (https://t.me/hypervoter_opensource)');
$climate->out('');
$option = getopt('g::');
if (isset($option['g'])) {
    generator($ig, $climate);
} else {
    run($ig, $climate);
}
/**
 * Json config generator
 */
function generator($ig, $climate)
{
    $climate->backgroundBlueWhite('Hypervote Config Generator is Starting... ');
    $climate->out('');
    sleep(1);
    $climate->out('Please provide login data of your Instagram Account.');
    $login = getVarFromUser('Login');
    if (empty($login)) {
        do {
            $login = getVarFromUser('Login');
        } while (empty($login));
    }
    sleep(1);
    $password = getVarFromUser('Password');
    if (empty($password)) {
        do {
            $password = getVarFromUser('Password');
        } while (empty($password));
    }
    $first_loop = true;
    do {
        if ($first_loop) {
            $climate->out("(Optional) Set proxy, if needed. It's better to use a proxy from the same country where you running this script.");
            $climate->out('Proxy should match following pattern:');
            $climate->out('http://ip:port or http://username:password@ip:port');
            $climate->out("Don't use in pattern https://.");
            $climate->out("Type 3 to skip and don't use proxy.");
            $first_loop = false;
        } else {
            $climate->out('Proxy - [NOT VALID]');
            $climate->out('Please check the proxy syntax and try again.');
        }
        $proxy = getVarFromUser('Proxy');
        if (empty($proxy)) {
            do {
                $proxy = getVarFromUser('Proxy');
            } while (empty($proxy));
        }
        if ('3' === $proxy) {
            $proxy = '3';
            break;
        }
    } while (!isValidProxy($proxy, $climate));
    $climate->out('Please choose the Hypervote estimated speed.');
    $climate->out('Type integer value without spaces from 1 to 1 500 000 stories/day or 0 for maximum possible speed.');
    $climate->out('We recommend you set 400000 stories/day. This speed works well for a long time without exceeding the limits.');
    $climate->out('When you are using the maximum speed you may exceed the Hypervote limits per day if this account actively used by a user in the Instagram app at the same time.');
    $climate->out('If you are using another type of automation, we recommend to you reducing Hypervote speed and find your own golden ratio.');
    $speed = (int) getVarFromUser('Speed');
    if ($speed > 1500000) {
        do {
            $climate->out('Speed value is incorrect. Type integer value from 1 to 1 500 000 stories/day.');
            $climate->out('Type 0 for maximum speed.');
            $speed = (int) getVarFromUser('Delay');
        } while ($speed > 1500000);
    }
    if (0 == $speed) {
        $climate->out('Maximum speed enabled.');
    } else {
        $climate->out('Speed set to ' . $speed . ' stories/day.');
    }
    $climate->out('Experimental features:');
    $climate->out('Voting only fresh stories, which posted no more than X hours ago.');
    $climate->out('X - is integer value from 1 to 23.');
    $climate->out('Type 0 to skip this option.');
    $climate->out('This option will reduce speed, but can increase results of Hypervote.');
    $fresh_stories_range = 0;
    if ($fresh_stories_range > 23) {
        do {
            $climate->out('Fresh stories range value is incorrect. Type integer value from 1 to 23.');
            $climate->out('Type 0 for skip this option.');
            $fresh_stories_range = 0;
        } while ($fresh_stories_range > 23);
    }
    $defined_targs = getVarFromUser('Please define your targets. Use only usernames without "@" sign');
    $q_answers = (int) getVarFromUser('Is Question Answers active? (0/1)');
    $q_vote = (int) getVarFromUser('Is Poll Vote active? (0/1)');
    $q_slide = (int) getVarFromUser('Is Slide Points active? (0/1)');
    $q_quiz = (int) getVarFromUser('Is Quiz Answers active? (0/1)');
    $q_stories = (int) getVarFromUser('Is Story Masslooking Active? (0/1)');
    $climate->out('Please use this option with caution.Our algorithm is optimized for maximum efficiency and human behaviour. As developers, we are not responsible if your account blocked by Instagram.');
    if (0 !== $q_answers) {
        $q_answers_a = getVarFromUser('Please provide your answers (comma seperated. For Ex: hello,hi there,oh dear)');
    }
    if (0 !== $q_slide) {
        $q_slide_points_min = (int) getVarFromUser('Please Provide Min. Slide Points (0/100)');
        $q_slide_points_max = (int) getVarFromUser('Please Provide Max. Slide Points (0/100)');
        do {
            $climate->errorBold('Max value can not set lower than min value. Max value must set ' . ($q_slide_points_min + 1) . ' or bigger!');
            $q_slide_points_max = (int) getVarFromUser('Please Provide Max. Slide Points (0/100)');
        } while ($q_slide_points_min > $q_slide_points_max);
    } else {
        $q_slide_points_min = 0;
        $q_slide_points_max = 100;
    }
    if (!empty($q_answers_a)) {
        $qs = explode(',', $q_answers_a);
    } else {
        $qs = array();
    }
    $data = array(
        'username' => $login,
        'password' => $password,
        'proxy' => $proxy,
        'speed_value' => $speed,
        'targets' => $defined_targs,
        'fresh_stories_range' => 5,
        'is_poll_vote_active' => (0 === $q_vote) ? false : true,
        'is_slider_points_active' => (0 === $q_slide) ? false : true,
        'is_questions_answers_active' => (0 === $q_answers) ? false : true,
        'is_quiz_answers_active' => (0 === $q_quiz) ? false : true,
        'is_mass_story_vivew_active' => (0 === $q_stories) ? false : true,
        'questions_answers' => $qs,
        'slider_points_range' => array(
            ($q_slide_points_min) ? $q_slide_points_min : 0,
            ($q_slide_points_max) ? $q_slide_points_max : 100,
        ),
    );
    $choicebaby = $climate->confirm('All values are set. Do you want to save this configuration?');
    if ($choicebaby->confirmed()) {
        $filename = getVarFromUser('Please set a name for file');
        if (file_exists(__DIR__ .'/config/config-' . $filename . '.json')) {
            $climate->errorBold('File ' . $filename . ' already exists. Set a different name');
            $filename = getVarFromUser('Please set a name for file');
        }
        $fp = fopen(__DIR__ .'/config/config-' . $filename . '.json', 'w');
        fwrite($fp, json_encode($data));
        fclose($fp);
        $climate->infoBold('Config file ' . $filename . ' successfully saved. Hyperloop starting in 3 seconds...');
        sleep(3);
        run($ig, $climate, $filename, null);
    } else {
        $choice2 = $climate->confirm(' All your changes not saved. Are you sure? ');
        if ($choice2->confirmed()) {
            $climate->info(' Allright. Hyperloop sequence starting with these info in 3 seconds... ');
            sleep(3);
            run($ig, $climate, null, json_encode($data));
        } else {
            $filename = getVarFromUser('Please set a name for file');
            if (file_exists(__DIR__ .'/config/config-' . $filename . '.json')) {
                $climate->errorBold('File ' . $filename . ' already exists. Set a different name');
                $filename = getVarFromUser('Please set a name for file');
            }
            $fp = fopen(__DIR__ .'/config/config-' . $filename . '.json', 'w');
            fwrite($fp, json_encode($data));
            fclose($fp);
            $climate->infoBold('Config file ' . $filename . ' successfully saved. Hyperloop starting in 3 seconds...');
            sleep(3);
            run($ig, $climate, $filename, null);
        }
    }
} // Generator Ends
/**
 * Json config generator
 */
/**
 * Let's start the show
 */
function run($ig, $climate, $conf_name = null, $datajson = null)
{
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', '-1');
    try {
        if (null == $datajson) {
            if (null !== $conf_name) {
                $climate->out('Config file name provided by generator. Processing...');
                $config_name = $conf_name;
            } else {
                $climate->out('Please provide an username for config file...');
                $config_name = getVarFromUser('Username');
            }
            if (empty($config_name)) {
                do {
                    $config_name = getVarFromUser('Username');
                } while (empty($config_name));
            }
            $climate->infoBold('Checking for config...');
            sleep(1);
            if (file_exists(__DIR__ .'/config/config-' . $config_name . '.json')) {
                $climate->infoBold('Config file found. Processing...');
                sleep(1);
                $mafile = fopen(__DIR__ .'/config/config-' . $config_name . '.json', 'r');
                $file = fread($mafile, filesize(__DIR__ .'/config/config-' . $config_name . '.json'));
                $data = json_decode($file);
                fclose($mafile);
                sleep(1);
                    if ('' !== $data->username) {
                        $climate->infoBold('Username Found');
                        $login = $data->username;
                    } else {
                        $climate->backgroundRedWhiteBold('Username can not empty');
                        sleep(1);
                        exit();
                    }
                    if ('' !== $data->password) {
                        $climate->infoBold('Password Found');
                        $password = $data->password;
                    } else {
                        $climate->backgroundRedWhiteBold('Password can not empty');
                        sleep(1);
                        exit();
                    }
                    if ('3' === $data->proxy) {
                        $climate->infoBold('Proxy Skipping Enabled. Processing ...');
                        $proxy = '3';
                        sleep(1);
                    } else {
                        $climate->infoBold('Proxy Option found. Validating...');
                        sleep(1);
                        $validate_proxy = isValidProxy($data->proxy, $climate);
                        $climate->infoBold('Proxy Status : ' . $validate_proxy);
                        if (200 == $validate_proxy) {
                            $climate->info('Proxy Connected. Processing ...');
                            $proxy = $data->proxy;
                            $ig->setProxy($data->proxy);
                        } else {
                            $proxy = '3';
                            $climate->info('Proxy can not conntected. Skipping...');
                        }
                    }
                    if ($data->speed_value) {
                        $climate->infoBold('Speed Value Found. Processing ... ');
                        $speed = (int) $data->speed_value;
                        if ($speed > 1500000) {
                            do {
                                $climate->errorBold('Speed value is incorrect. Type integer value from 1 to 1 500 000 stories/day.');
                                usleep(500000);
                                $climate->errorBold('For Maxiumum speed please use "0"... Please set speed now (that will not change your config file)');
                                $speed = (int) getVarFromUser('Speed');
                            } while ($speed > 1500000);
                        }
                        if (0 === $speed) {
                            $climate->infoBold('  Maximum speed enabled.  ');
                            $delay = 46;
                        } else {
                            $climate->infoBold('Speed set to ' . $speed . ' stories/day.');
                            $delay = round(60 * 60 * 24 * 200 / $speed);
                        }
                    }
                    if ($data->fresh_stories_range > 0) {
                        $fresh_stories_range = 0;
                        $climate->infoBold('Experimental Feature (Fresh Stories Range) value found. Validating ...');
                        sleep(1);
                        if ($fresh_stories_range > 23) {
                            do {
                                $climage->errorBold('Fresh stories range value is incorrect. Type integer value from 1 to 23.');
                                $climage->errorBold('Type 0 for skip this option.');
                                $fresh_stories_range = 0;
                            } while ($fresh_stories_range > 23);
                        }
                        $climate->infoBold('Fresh Stories Range set to ' . $fresh_stories_range);
                        sleep(1);
                    } else {
                        $fresh_stories_range = 0;
                        $climate->infoBold('Experimental Feature (Fresh Stories Range) Skipping.');
                        usleep(500000);
                    }
                    $defined_targets = $data->targets;
                    if ($data->is_mass_story_vivew_active) {
                        $climate->backgroundRedWhiteBold('Attention! Mass story View Feature is active. Please use this option with caution. Our algorithm is optimized for maximum efficiency and human behaviour. As developers, we are not responsible if your account blocked by Instagram. ');
                    }
            } else {
                $climate->backgroundRedWhiteBold(' Config file not found. Manual input starting... ');
                sleep(5);
                $defined_targets = null;
                $climate->out('Please provide login data of your Instagram Account.');
                $login = getVarFromUser('Login');
                if (empty($login)) {
                    do {
                        $login = getVarFromUser('Login');
                    } while (empty($login));
                }
                $password = getVarFromUser('Password');
                if (empty($password)) {
                    do {
                        $password = getVarFromUser('Password');
                    } while (empty($password));
                }
                $first_loop = true;
                do {
                    if ($first_loop) {
                        $climate->out("(Optional) Set proxy, if needed. It's better to use a proxy from the same country where you running this script.");
                        $climate->out('Proxy should match following pattern:');
                        $climate->out('http://ip:port or http://username:password@ip:port');
                        $climate->out("Don't use in pattern https://.");
                        $climate->out("Type 3 to skip and don't use proxy.");
                        $first_loop = false;
                    } else {
                        $climate->out('Proxy - [NOT VALID]');
                        $climate->out('Please check the proxy syntax and try again.');
                    }
                    $proxy = getVarFromUser('Proxy');
                    if (empty($proxy)) {
                        do {
                            $proxy = getVarFromUser('Proxy');
                        } while (empty($proxy));
                    }
                    if ('3' === $proxy) {
                        // Skip proxy setup
                        $proxy = '3';
                        break;
                    }
                } while (!isValidProxy($proxy, $climate));
                $proxy_check = isValidProxy($proxy, $climate);
                if ('3' === $proxy) {
                    $proxy = '3';
                    $climate->info('Proxy Setup Skipped');
                } elseif (500 === $proxy_check) {
                    $proxy = '3';
                    $climate->info('Proxy is not valid. Skipping');
                } else {
                    $climate->info('Proxy - [OK]');
                    $ig->setProxy($proxy);
                }
                $climate->out('Please choose the Hypervote estimated speed.');
                $climate->out('Type integer value without spaces from 1 to 1 500 000 stories/day or 0 for maximum possible speed.');
                $climate->out('We recommend you set 400000 stories/day. This speed works well for a long time without exceeding the limits.');
                $climate->out('When you are using the maximum speed you may exceed the Hypervote limits per day if this account actively used by a user in the Instagram app at the same time.');
                $climate->out('If you are using another type of automation, we recommend to you reducing Hypervote speed and find your own golden ratio.');
                $speed = (int) getVarFromUser('Speed');
                if ($speed > 1500000) {
                    do {
                        $climate->out('Speed value is incorrect. Type integer value from 1 to 1 500 000 stories/day.');
                        $climate->out('Type 0 for maximum speed.');
                        $speed = (int) getVarFromUser('Delay');
                    } while ($speed > 1500000);
                }
                if (0 == $speed) {
                    $climate->out('Maximum speed enabled.');
                    $delay = 46;
                } else {
                    $climate->out('Speed set to ' . $speed . ' stories/day.');
                    $delay = round(60 * 60 * 24 * 200 / $speed);
                }
                $climate->out('Experimental features:');
                $climate->out('Voting only fresh stories, which posted no more than X hours ago.');
                $climate->out('X - is integer value from 1 to 23.');
                $climate->out('Type 0 to skip this option.');
                $climate->out('This option will reduce speed, but can increase results of Hypervote.');
                $fresh_stories_range = 0;
                if ($fresh_stories_range > 23) {
                    do {
                        $climate->out('Fresh stories range value is incorrect. Type integer value from 1 to 23.');
                        $climate->out('Type 0 for skip this option.');
                        $fresh_stories_range = 0;
                    } while ($fresh_stories_range > 23);
                }
                $q_answers = (int) getVarFromUser('Is Question Answers active? (0/1)');
                $q_vote = (int) getVarFromUser('Is Poll Vote active? (0/1)');
                $q_slide = (int) getVarFromUser('Is Slide Points active? (0/1)');
                $q_quiz = (int) getVarFromUser('Is Quiz Answers active? (0/1)');
                $q_stories = (int) getVarFromUser('Is Story Masslooking Active? (0/1)');
                $climate->out('Please use this option with caution. Our algorithm is optimized for maximum efficiency and human behaviour. As developers, we are not responsible if your account blocked by Instagram.');
                if (0 !== $q_answers) {
                    $q_answers_a = getVarFromUser('Please provide your answers (in comma seperated)');
                }
                if (0 !== $q_slide) {
                    $q_slide_points_min = (int) getVarFromUser('Please Provide Min. Slide Points (0/100)');
                    $q_slide_points_max = (int) getVarFromUser('Please Provide Max. Slide Points (0/100)');
                    do {
                        $climate->errorBold('Max value can not set lower than min value. Max value must set ' . ($q_slide_points_min) . ' or bigger!');
                        $q_slide_points_max = (int) getVarFromUser('Please Provide Max. Slide Points (0/100)');
                    } while ($q_slide_points_min > $q_slide_points_max);
                } else {
                    $q_slide_points_min = 0;
                    $q_slide_points_max = 100;
                }
                if (!empty($q_answers_a)) {
                    $qs = explode(',', $q_answers_a);
                } else {
                    $qs = array();
                }
                $datas = json_encode(
                    array(
                        'is_poll_vote_active' => (0 === $q_vote) ? false : true,
                        'is_slider_points_active' => (0 === $q_slide) ? false : true,
                        'is_questions_answers_active' => (0 === $q_answers) ? false : true,
                        'is_quiz_answers_active' => (0 === $q_quiz) ? false : true,
                        'is_mass_story_vivew_active' => (0 === $q_stories) ? false : true,
                        'questions_answers' => $qs,
                        'slider_points_range' => array(
                            ($q_slide_points_min) ? $q_slide_points_min : 0,
                            ($q_slide_points_max) ? $q_slide_points_max : 100,
                        ),
                    )
                );
                $data = json_decode($datas);
            }
        } else {
            $data = json_decode($datajson);
            $defined_targets = $data->targets;
            $login = $data->username;
            $password = $data->password;
            $proxy = $data->proxy;
        }
        $login_process = validate_login_process($ig, $login, $password, $climate, $proxy, false);
        $is_connected = $login_process;
        if ($is_connected) {
            $climate->infoBold('Logged as @' . $login . ' successfully.');
        }
        $data_targ = define_targets($ig, $login, $defined_targets, $climate);
        hypervote_v1($data, $data_targ, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy);
    } catch (\Exception $e) {
        $climate->errorBold($e->getMessage());
        sleep(1);
        $climate->errorBold('Please run script command again.');
        exit;
    }
}
function validate_login_process($ig, $login, $password, $climate, $proxy, $slient)
{
    $is_connected = false;
    $is_connected_count = 0;
    $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
    do {
        if (10 == $is_connected_count) {
            if ($e->getResponse()) {
                $climate->errorBold($e->getMessage());
            }
            throw new Exception($fail_message);
        }
        try {
            if (0 == $is_connected_count) {
                if ($slient) {
                } else {
                    $climate->infoBold('Emulation of an Instagram app initiated...');
                }
            }
            $login_resp = $ig->login($login, $password);
            if (null !== $login_resp && $login_resp->isTwoFactorRequired()) {
                // Default verification method is phone
                $twofa_method = '1';
                // Detect is Authentification app verification is available
                $is_totp = json_decode(json_encode($login_resp), true);
                if ('1' == $is_totp['two_factor_info']['totp_two_factor_on']) {
                    $climate->infoBold('Two-factor authentication required, please enter the code from you Authentication app');
                    $twofa_id = $login_resp->getTwoFactorInfo()->getTwoFactorIdentifier();
                    $twofa_method = '3';
                } else {
                    $climate->bold(
                        'Two-factor authentication required, please enter the code sent to your number ending in %s',
                        $login_resp->getTwoFactorInfo()->getObfuscatedPhoneNumber()
                    );
                    $twofa_id = $login_resp->getTwoFactorInfo()->getTwoFactorIdentifier();
                }
                $twofa_code = getVarFromUser('Two-factor code');
                if (empty($twofa_code)) {
                    do {
                        $twofa_code = getVarFromUser('Two-factor code');
                    } while (empty($twofa_code));
                }
                $is_connected = false;
                $is_connected_count = 0;
                do {
                    if (10 == $is_connected_count) {
                        if ($e->getResponse()) {
                            $climate->errorBold($e->getMessage());
                        }
                        throw new Exception($fail_message);
                    }
                    if (0 == $is_connected_count) {
                        $climate->infoBold('Two-factor authentication in progress...');
                    }
                    try {
                        $twofa_resp = $ig->finishTwoFactorLogin($login, $password, $twofa_id, $twofa_code, $twofa_method);
                        $is_connected = true;
                    } catch (\InstagramAPI\Exception\NetworkException $e) {
                        sleep(7);
                    } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                        sleep(7);
                    } catch (\InstagramAPI\Exception\InvalidSmsCodeException $e) {
                        $is_code_correct = false;
                        $is_connected = true;
                        do {
                            $climate->errorBold('Code is incorrect. Please check the syntax and try again.');
                            $twofa_code = getVarFromUser('Two-factor code');
                            if (empty($twofa_code)) {
                                do {
                                    $twofa_code = getVarFromUser('Security code');
                                } while (empty($twofa_code));
                            }
                            $is_connected = false;
                            $is_connected_count = 0;
                            do {
                                try {
                                    if (10 == $is_connected_count) {
                                        if ($e->getResponse()) {
                                            $climte->out($e->getMessage());
                                        }
                                        throw new Exception($fail_message);
                                    }
                                    if (0 == $is_connected_count) {
                                        $climate->infoBold('Verification in progress...');
                                    }
                                    $twofa_resp = $ig->finishTwoFactorLogin($login, $password, $twofa_id, $twofa_code, $twofa_method);
                                    $is_code_correct = true;
                                    $is_connected = true;
                                } catch (\InstagramAPI\Exception\NetworkException $e) {
                                    sleep(7);
                                } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                                    sleep(7);
                                } catch (\InstagramAPI\Exception\InvalidSmsCodeException $e) {
                                    $is_code_correct = false;
                                    $is_connected = true;
                                } catch (\Exception $e) {
                                    throw $e;
                                }
                                $is_connected_count += 1;
                            } while (!$is_connected);
                        } while (!$is_code_correct);
                    } catch (\Exception $e) {
                        throw $e;
                    }
                    $is_connected_count += 1;
                } while (!$is_connected);
            }
            $is_connected = true;
        } catch (\InstagramAPI\Exception\NetworkException $e) {
            sleep(7);
        } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
            sleep(7);
        } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
            throw new Exception('Please go to Instagram website or mobile app and pass checkpoint!');
        } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
            if (!($ig instanceof InstagramAPI\Instagram)) {
                throw new Exception('Oops! Something went wrong. Please try again later! (invalid_instagram_client)');
            }
            if (!($e instanceof InstagramAPI\Exception\ChallengeRequiredException)) {
                throw new Exception('Oops! Something went wrong. Please try again later! (unexpected_exception)');
            }
            if (!$e->hasResponse() || !$e->getResponse()->isChallenge()) {
                throw new Exception('Oops! Something went wrong. Please try again later! (unexpected_exception_response)');
            }
            $challenge = $e->getResponse()->getChallenge();
            if (is_array($challenge)) {
                $api_path = $challenge['api_path'];
            } else {
                $api_path = $challenge->getApiPath();
            }
            $climate->info('Instagram want to send you a security code to verify your identity.');
            $climate->info('How do you want receive this code?');
            $climate->infoBold('1 - [Email]');
            $climate->infoBold('2 - [SMS]');
            $climate->infoBold('3 - [Exit]');
            $choice = getVarFromUser('Choice');
            if (empty($choice)) {
                do {
                    $choice = getVarFromUser('Choice');
                } while (empty($choice));
            }
            if ('1' == $choice || '2' == $choice || '3' == $choice) {
                // All fine
            } else {
                $is_choice_ok = false;
                do {
                    $climate->errorBold('Choice is incorrect. Type 1, 2 or 3.');
                    $choice = getVarFromUser('Choice');
                    if (empty($choice)) {
                        do {
                            $choice = getVarFromUser('Choice');
                        } while (empty($choice));
                    }
                    if ('1' == $confirm || '2' == $confirm || '3' == $confirm) {
                        $is_choice_ok = true;
                    }
                } while (!$is_choice_ok);
            }
            $challange_choice = 0;
            if ('3' == $choice) {
                run($ig, $climate);
            } elseif ('1' == $choice) {
                // Email
                $challange_choice = 1;
            } else {
                // SMS
                $challange_choice = 0;
            }
            $is_connected = false;
            $is_connected_count = 0;
            do {
                if (10 == $is_connected_count) {
                    if ($e->getResponse()) {
                        $climate->errorBold($e->getMessage());
                    }
                    throw new Exception($fail_message);
                }
                try {
                    $challenge_resp = $ig->sendChallangeCode($api_path, $challange_choice);
                    // Failed to send challenge code via email. Try with SMS.
                    if ('ok' != $challenge_resp->status) {
                        $challange_choice = 0;
                        sleep(7);
                        $challenge_resp = $ig->sendChallangeCode($api_path, $challange_choice);
                    }
                    $is_connected = true;
                } catch (\InstagramAPI\Exception\NetworkException $e) {
                    sleep(7);
                } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                    sleep(7);
                } catch (\Exception $e) {
                    throw $e;
                }
                $is_connected_count += 1;
            } while (!$is_connected);
            if ('ok' != $challenge_resp->status) {
                if (isset($challenge_resp->message)) {
                    if ('This field is required.' == $challenge_resp->message) {
                        $climate->info("We received the response 'This field is required.'. This can happen in 2 reasons:");
                        $climate->info('1. Instagram already sent to you verification code to your email or mobile phone number. Please enter this code.');
                        $climate->info('2. Instagram forced you to phone verification challenge. Try login to Instagram app or website and take a look at what happened.');
                    }
                } else {
                    $climate->info('Instagram Response: ' . json_encode($challenge_resp));
                    $climate->info("Couldn't send a verification code for the login challenge. Please try again later.");
                    $climate->info('- Is this account has attached mobile phone number in settings?');
                    $climate->info('- If no, this can be a reason of this problem. You should add mobile phone number in account settings.');
                    throw new Exception('- Sometimes Instagram can force you to phone verification challenge process.');
                }
            }
            if (isset($challenge_resp->step_data->contact_point)) {
                $contact_point = $challenge_resp->step_data->contact_point;
                if (2 == $choice) {
                    $climate->info('Enter the code sent to your number ending in ' . $contact_point . '.');
                } else {
                    $climate->info('Enter the 6-digit code sent to the email address ' . $contact_point . '.');
                }
            }
            $security_code = getVarFromUser('Security code');
            if (empty($security_code)) {
                do {
                    $security_code = getVarFromUser('Security code');
                } while (empty($security_code));
            }
            if ('3' == $security_code) {
                throw new Exception('Reset in progress...');
            }
            // Verification challenge
            $ig = challange($ig, $login, $password, $api_path, $security_code, $proxy, $climate);
        } catch (\InstagramAPI\Exception\AccountDisabledException $e) {
            throw new Exception('Your account has been disabled for violating Instagram terms. Go Instagram website or mobile app to learn how you may be able to restore your account.');
        } catch (\InstagramAPI\Exception\ConsentRequiredException $e) {
            throw new Exception('Instagram updated Terms and Data Policy. Please go to Instagram website or mobile app to review these changes and accept them.');
        } catch (\InstagramAPI\Exception\SentryBlockException $e) {
            throw new Exception('Access to Instagram API restricted for spam behavior or otherwise abusing. You can try to use Session Catcher script (available by https://nextpost.tech/session-catcher) to get valid Instagram session from location, where your account created from.');
        } catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
            throw new Exception('The password you entered is incorrect. Please try again.');
        } catch (\InstagramAPI\Exception\InvalidUserException $e) {
            throw new Exception("The username you entered doesn't appear to belong to an account. Please check your username in config file and try again.");
        } catch (\Exception $e) {
            throw $e;
        }
        $is_connected_count += 1;
    } while (!$is_connected);
}
/**
 * Define targets for Hypervote
 */
function define_targets($ig, $username, $defined_targets = null, $climate)
{
    do {
        if (null === $defined_targets) {
            $climate->out('Please define the targets.');
            $climate->out("Write all Instagram profile usernames via comma without '@' symbol.");
            $climate->out('Example: apple, instagram, hostazor');
            $targets_input = getVarFromUser('Usernames');
            if (empty($targets_input)) {
                do {
                    $targets_input = getVarFromUser('Usernames');
                } while (empty($targets_input));
            }
        } else {
            $climate->infoBold('Targets already defined config file. Applying...');
            sleep(1);
            $targets_input = $defined_targets;
        }
        $targets_input = str_replace(' ', '', $targets_input);
        $targets = [];
        $targets = explode(',', trim($targets_input));
        $targets = array_unique($targets);
        $pks = [];
        $filtered_targets = [];
        foreach ($targets as $target) {
            $is_connected = false;
            $is_connected_count = 0;
            if ($target === $username) {
                $climate->errorBold('Please do not add yourself into targets. Your username is skipping...');
                continue;
            }
            do {
                if (10 == $is_connected_count) {
                    if ($e->getResponse()) {
                        $climate->errorBold($e->getMessage());
                    }
                    $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
                    $climate->errorBold($fail_message);
                    run($ig, $climate);
                }
                try {
                    $user_resp = $ig->people->getUserIdForName($target);
                    $climate->info('@' . $target . ' - [OK]');
                    $filtered_targets[] = $target;
                    $pks[] = $user_resp;
                    $is_connected = true;
                    if (($target != $targets[count($targets) - 1]) && (count($targets) > 0)) {
                        sleep(1);
                    }
                } catch (\InstagramAPI\Exception\NetworkException $e) {
                    sleep(7);
                } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                    sleep(7);
                } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
                    $climate->error('Please login again and pass verification challenge. Instagram will send you a security code to verify your identity.');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                    $climate->error('Please go to Instagram website or mobile app and pass checkpoint!');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\AccountDisabledException $e) {
                    $climate->error('Your account has been disabled for violating Instagram terms. Go Instagram website or mobile app to learn how you may be able to restore your account.');
                    $climate->error('Use this form for recovery your account: https://help.instagram.com/contact/1652567838289083');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\ConsentRequiredException $e) {
                    $climate->error('Instagram updated Terms and Data Policy. Please go to Instagram website or mobile app to review these changes and accept them.');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\SentryBlockException $e) {
                    $climate->error('Access to Instagram API restricted for spam behavior or otherwise abusing. You can try to use Session Catcher script (available by https://nextpost.tech/session-catcher) to get valid Instagram session from location, where your account created from.');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\ThrottledException $e) {
                    $climate->error('Throttled by Instagram because of too many API requests.');
                    $climate->error('Please login again after 1 hour. You reached Instagram limits.');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\NotFoundException $e) {
                    $is_connected = true;
                    $is_username_correct = false;
                    do {
                        $climate->error('Instagram profile username @' . $target . ' is incorrect or maybe user just blocked you (Login to Instagram website or mobile app and check that).');
                        $climate->error('Type 3 for skip this target.');
                        $target_new = getVarFromUser('Please provide valid username');
                        if (empty($target_new)) {
                            do {
                                $target_new = getVarFromUser('Please provide valid username');
                            } while (empty($target_new));
                        }
                        if ('3' == $target_new) {
                            break;
                        } else {
                            $is_connected = false;
                            $is_connected_count = 0;
                            do {
                                if (10 == $is_connected_count) {
                                    if ($e->getResponse()) {
                                        $climate->errorBold($e->getMessage());
                                    }
                                    $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
                                    $climate->errorBold($fail_message);
                                    run($ig, $climate);
                                }
                                try {
                                    $user_resp = $ig->people->getUserIdForName($target_new);
                                    $climate->info('@' . $target_new . ' - [OK]');
                                    $filtered_targets[] = $target_new;
                                    $pks[] = $user_resp;
                                    $is_username_correct = true;
                                    $is_connected = true;
                                } catch (\InstagramAPI\Exception\NetworkException $e) {
                                    sleep(7);
                                } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                                    sleep(7);
                                } catch (InstagramAPI\Exception\NotFoundException $e) {
                                    $is_username_correct = false;
                                    $is_connected = true;
                                } catch (\Exception $e) {
                                    $climate->error($e->getMessage());
                                    run($ig, $climate);
                                }
                                $is_connected_count += 1;
                            } while (!$is_connected);
                        }
                    } while (!$is_username_correct);
                } catch (Exception $e) {
                    $climate->errorBold($e->getMessage());
                    run($ig, $climate);
                }
                $is_connected_count += 1;
            } while (!$is_connected);
        }
    } while (empty($filtered_targets));
    $targets = array_unique($filtered_targets);
    $pks = array_unique($pks);
    $data_targ = [];
    for ($i = 0; $i < count($targets); $i++) {
        $data_targ[$i] = [
            'username' => $targets[$i],
            'pk' => $pks[$i],
        ];
    }
    $climate->info('Selected ' . count($targets) . ' targets: @' . implode(', @', $targets) . '.');
    $climate->info('Please confirm that the selected targets are correct.');
    $climate->info('1 - [Yes]');
    $climate->info('2 - [No]');
    $climate->info('3 - [Exit]');
    $confirm = getVarFromUser('Choice');
    if (empty($confirm)) {
        do {
            $confirm = getVarFromUser('Choice');
        } while (empty($confirm));
    }
    if ('1' === $confirm || '2' === $confirm || '3' === $confirm) {
        // All fine
    } else {
        $is_choice_ok = false;
        do {
            $climate->error('Choice is incorrect. Type 1, 2 or 3.');
            $confirm = getVarFromUser('Choice');
            if (empty($confirm)) {
                do {
                    $confirm = getVarFromUser('Choice');
                } while (empty($confirm));
            }
            if ('1' === $confirm || '2' === $confirm || '3' === $confirm) {
                $is_choice_ok = true;
            }
        } while (!$is_choice_ok);
    }
    if ('3' === $confirm) {
        run($ig, $climate);
    } elseif ('2' === $confirm) {
        $data_targ = define_targets($ig, $username, $defined_targets, $climate);
    } else {
        // All fine. Going to Hypervote.
    }
    return $data_targ;
}
/**
 * Get varable from user
 */
function getVarFromUser($text)
{
    echo $text . ': ';
    $var = trim(fgets(STDIN));
    return $var;
}
/**
 * Validates proxy address
 */
function isValidProxy($proxy, $climate, $slient = false)
{
    if (false === $slient) {
        $climate->info('Connecting to Instagram...');
    }
    $code = null;
    try {
        $client = new \GuzzleHttp\Client();
        $res = $client->request(
            'GET',
            'http://www.instagram.com',
            [
                'timeout' => 60,
                'proxy' => $proxy,
            ]
        );
        $code = $res->getStatusCode();
        $is_connected = true;
    } catch (\Exception $e) {
        //$climate->error( $e->getMessage() );
        $code = '500';
        //return false;
    }
    return $code;
}
/**
 * Validates proxy address
 */
function finishLogin($ig, $login, $password, $proxy, $climate)
{
    $is_connected = false;
    $is_connected_count = 0;
    try {
        do {
            if (10 == $is_connected_count) {
                if ($e->getResponse()) {
                    $climate->out($e->getMessage());
                }
                $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
                $climate->errorBold($fail_message);
                run($ig, $climate);
            }
            if ('3' == $proxy) {
                // Skip proxy setup
            } else {
                $ig->setProxy($proxy);
            }
            try {
                $login_resp = $ig->login($login, $password);
                if (null !== $login_resp && $login_resp->isTwoFactorRequired()) {
                    // Default verification method is phone
                    $twofa_method = '1';
                    // Detect is Authentification app verification is available
                    $is_totp = json_decode(json_encode($login_resp), true);
                    if ('1' == $is_totp['two_factor_info']['totp_two_factor_on']) {
                        $climate->info('Two-factor authentication required, please enter the code from you Authentication app');
                        $twofa_id = $login_resp->getTwoFactorInfo()->getTwoFactorIdentifier();
                        $twofa_method = '3';
                    } else {
                        $climate->info(
                            'Two-factor authentication required, please enter the code sent to your number ending in %s',
                            $login_resp->getTwoFactorInfo()->getObfuscatedPhoneNumber()
                        );
                        $twofa_id = $login_resp->getTwoFactorInfo()->getTwoFactorIdentifier();
                    }
                    $twofa_code = getVarFromUser('Two-factor code');
                    if (empty($twofa_code)) {
                        do {
                            $twofa_code = getVarFromUser('Two-factor code');
                        } while (empty($twofa_code));
                    }
                    $is_connected = false;
                    $is_connected_count = 0;
                    do {
                        if (10 == $is_connected_count) {
                            if ($e->getResponse()) {
                                $climate->errorBold($e->getMessage());
                            }
                            $climate->errorBold($fail_message);
                            run($ig, $climate);
                        }
                        if (0 == $is_connected_count) {
                            $climate->info('Two-factor authentication in progress...');
                        }
                        try {
                            $twofa_resp = $ig->finishTwoFactorLogin($login, $password, $twofa_id, $twofa_code, $twofa_method);
                            $is_connected = true;
                        } catch (\InstagramAPI\Exception\NetworkException $e) {
                            sleep(7);
                        } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                            sleep(7);
                        } catch (\InstagramAPI\Exception\InvalidSmsCodeException $e) {
                            $is_code_correct = false;
                            $is_connected = true;
                            do {
                                $cliate->errorBold('Code is incorrect. Please check the syntax and try again.');
                                $twofa_code = getVarFromUser('Two-factor code');
                                if (empty($twofa_code)) {
                                    do {
                                        $twofa_code = getVarFromUser('Security code');
                                    } while (empty($twofa_code));
                                }
                                $is_connected = false;
                                $is_connected_count = 0;
                                do {
                                    try {
                                        if (10 == $is_connected_count) {
                                            if ($e->getResponse()) {
                                                $climate->error($e->getMessage());
                                            }
                                            $climate->errorBold($fail_message);
                                            run($ig, $climate);
                                        }
                                        if (0 == $is_connected_count) {
                                            $climate->info('Verification in progress...');
                                        }
                                        $twofa_resp = $ig->finishTwoFactorLogin($login, $password, $twofa_id, $twofa_code, $twofa_method);
                                        $is_code_correct = true;
                                        $is_connected = true;
                                    } catch (\InstagramAPI\Exception\NetworkException $e) {
                                        sleep(7);
                                    } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                                        sleep(7);
                                    } catch (\InstagramAPI\Exception\InvalidSmsCodeException $e) {
                                        $is_code_correct = false;
                                        $is_connected = true;
                                    } catch (\Exception $e) {
                                        throw new $e();
                                    }
                                    $is_connected_count += 1;
                                } while (!$is_connected);
                            } while (!$is_code_correct);
                        } catch (\Exception $e) {
                            throw $e;
                        }
                        $is_connected_count += 1;
                    } while (!$is_connected);
                }
                $is_connected = true;
            } catch (\InstagramAPI\Exception\NetworkException $e) {
                sleep(7);
            } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                sleep(7);
            } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                throw new Exception('Please go to Instagram website or mobile app and pass checkpoint!');
            } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
                $climate->error('Instagram Response: ' . json_encode($e->getResponse()));
                $climate->error("Couldn't complete the verification challenge. Please try again later.");
                throw new Exception('Developer code: Challenge loop.');
            } catch (\Exception $e) {
                throw $e;
            }
            $is_connected_count += 1;
        } while (!$is_connected);
    } catch (\Exception $e) {
        $climate->errorBold($e->getMessage());
        run($ig, $climate);
    }
    return $ig;
}
/**
 * Verification challenge
 */
function challange($ig, $login, $password, $api_path, $security_code, $proxy, $climate)
{
    $is_connected = false;
    $is_connected_count = 0;
    $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
    do {
        if (10 == $is_connected_count) {
            if ($e->getResponse()) {
                $climate->errorBold($e->getMessage());
            }
            throw new Exception($fail_message);
        }
        if (0 == $is_connected_count) {
            $climate->info('Verification in progress...');
        }
        try {
            $challenge_resp = $ig->finishChallengeLogin($login, $password, $api_path, $security_code);
            $is_connected = true;
        } catch (\InstagramAPI\Exception\NetworkException $e) {
            sleep(7);
        } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
            sleep(7);
        } catch (\InstagramAPI\Exception\InstagramException $e) {
            $msg = $e->getMessage();
            $climate->out($msg);
            $climate->out('Type 3 - to exit.');
            $security_code = getVarFromUser('Security code');
            if (empty($security_code)) {
                do {
                    $security_code = getVarFromUser('Security code');
                } while (empty($security_code));
            }
            if ('3' == $security_code) {
                throw new Exception('Reset in progress...');
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if ('Invalid Login Response at finishChallengeLogin().' == $msg) {
                sleep(7);
                $ig = finishLogin($ig, $login, $password, $proxy, $climate);
                $is_connected = true;
            } else {
                throw $e;
            }
        }
        $is_connected_count += 1;
    } while (!$is_connected);
    return $ig;
}
/**
 * Hypervote loop - Algorithm #2
 */
function hypervote_v1($data, $data_targ, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy = '3')
{
    $view_count = 0;
    $st_count = 0;
    $st_count_seen = 0;
    $begin = strtotime(date('Y-m-d H:i:s'));
    $begin_ms = strtotime(date('Y-m-d H:i:s'));
    $begin_mspoll = strtotime(date('Y-m-d H:i:s'));
    $begin_msslide = strtotime(date('Y-m-d H:i:s'));
    $begin_msquiz = strtotime(date('Y-m-d H:i:s'));
    $begin_msquestion = strtotime(date('Y-m-d H:i:s'));
    $begin_f = strtotime(date('Y-m-d H:i:s'));
    $time_from_logs = strtotime(date('Y-m-d H:i:s'));
    $speed = 0;
    $delitel = 0;
    $counter1 = 0;
    $counter2 = 0;
    $stories = [];
    $mycount = 0;
    $poll_votes_count = 0;
    $slider_points_count = 0;
    $question_answers_count = 0;
    $quiz_answers_count = 0;
    $story_vivews_count = 0;
    $follow_c_count = 0;
    $poll_throttled = false;
    $quiz_throttled = false;
    $slider_throttled = false;
	$fresh_stories_range = 0;
	$fresh_stories = 0;
    $question_throttled = false;
    $question_throttled = false;
    $countdown_throttled = false;
    $mass_view_throttled = false;
    $last_throttled_poll = time();
    $last_throttled_quiz = time();
    $last_throttled_slider = time();
    $last_throttled_question = time();
    $last_throttled_countdown = time();
    $last_throttled_mass_view = time();
	$last_throttled_follow = time();
	$throttled_windows = time();
    $begin_login = time();
    $usfile                  = '/vendor/mgp25/instagram-php/sessions/' . $ig->account_id.'-seed-stroies.txt';

    $climate->infoBold('Hypervote loop started.');
    $targets = [];
    $targets = $data_targ;
    shuffle($data_targ);
    for ($i = 0; $i < count($data_targ); $i++) {
        $data_targ[$i] += [
            'rank_token' => \InstagramAPI\Signatures::generateUUID(),
            'users_count' => 0,
            'max_id' => null,
            'begin_gf' => null,
        ];
    }
   do {
        foreach ($data_targ as $key => $d) {
            try {
                if (null == $d['max_id']) {
                    $is_gf_first = 1;
                }
                if (!empty($d['begin_gf'])) {
                    $current_time = strtotime(date('Y-m-d H:i:s'));
                    if (($current_time - $d['begin_gf']) < 7) {
                        $sleep_time = 7 - ($current_time - $d['begin_gf']) + mt_rand(1, 3);
                    }
                }
                /*try {
                $data_targ[ $key ]['begin_gf'] = strtotime( date( 'Y-m-d H:i:s' ) );
                $followers = $ig->people->getFollowers( $d['pk'], $d['rank_token'], null, $d['max_id'] );
                sleep(3);
                } catch ( \InstagramAPI\Exception\NotFoundException $e ) {
                $climate->error( '@' . $d['username'] . ' not found or maybe user blocked you (login to Instagram website or mobile app and check that).' );
                unset( $data_targ[ $key ] );
                continue;
                } catch ( Exception $e ) {
                throw $e;
                }*/
                // DEBUG - BEGIN
                //$followers_json = json_decode($followers);
                // $climate->out(var_dump($followers_json));
                // exit;
                // DEBUG - END
                /*if ( empty( $followers->getUsers() ) ) {
                $climate->error( '@' . $d['username'] . " don't have any follower." );
                unset( $data_targ[ $key ] );
                continue;
                }*/
                /*$follos = json_decode( $followers, true );*/
                //$climate->out(print_r($follos['users'],true));
                /*$data_targ[ $key ]['max_id'] = $follos['next_max_id'];*/
                /*$followers_ids = [];*/
                /*foreach ( $followers->getUsers() as $follower ) {
                $is_private                    = $follower->getIsPrivate();
                $has_anonymous_profile_picture = $follower->getHasAnonymousProfilePicture();
                $is_verified                   = $follower->getIsVerified();
                // Check is user have stories at scrapping
                $latest_reel_media = $follower->getLatestReelMedia();
                if ( ! ( $is_private ) && ! ( $has_anonymous_profile_picture ) && ! ( $is_verified ) && ( null !== $latest_reel_media ) ) {
                // Mark as seen only fresh stories, which posted no more than X hours ago
                if ( isset( $fresh_stories_range ) ) {
                $fresh_stories_min = time() - round( $fresh_stories_range * 60 * 60 );
                if ( $latest_reel_media >= strtotime( $fresh_stories_min ) ) {
                $followers_ids[] = $follower->getPk();
                }
                } else {
                $followers_ids[] = $follower->getPk();
                }
                }
                }*/
                $likers_list = null;
				$get_from_follow = false;
                // while ($next_max_id || $first_loop) {
                if (null !== $d['max_id']) {
                    //$climate->out("Pagination successfuly! @".$d['username']);
                } else {
                    //$climate->out("First page of @".$d['username']);
                }
				if (!$get_from_follow){
					try {
                $mediaFeed = $ig->timeline->getUserFeed($d['pk'], $d['max_id']);
				sleep(2);
                $items = $mediaFeed->getItems();
				if ($items == null) {
                    $climate->error($d['username']. "Cant fetch post. Sleeping 10 min..");
					sleep(10*60);
                    continue;
                }
                if (empty($items)) {
                    $climate->error($d['username']. "has no data in his/her profile. Skipping...");
                    continue;
                }
					}catch (\InstagramAPI\Exception\ThrottledException $e) {
						$get_from_follow=true;
						sleep(1);
				 }
                //$climate->out(print_r($mediaFeed->getNextMaxId()));
                $data_targ[$key]['max_id'] = $mediaFeed->getNextMaxId();
                //$climate->out("Next_max_id: " . $data[$key]['max_id']);
                $counsa = 100 % count($items);
                $vprocess = $climate->progress()->total(($counsa * count($items)));
                foreach ($items as $item) {
					try {
                    $mediaId = $item->getId();
                    $vprocess->advance(floor($counsa), 'Collecting data from @'.$d['username']);
                    $likers_list[] = $ig->media->getLikersChrono($mediaId);
					} catch (\InstagramAPI\Exception\ThrottledException $e) {
						$get_from_follow=true;
						sleep(1);
                }
				}
				
				
				
				} if ($get_from_follow){
					try {
				$data_targ[ $key ]['begin_gf'] = strtotime( date( 'Y-m-d H:i:s' ) );
                $followers = $ig->people->getFollowers( $d['pk'], $d['rank_token'], null, $d['max_id'] );
                sleep(10);
				} catch (\InstagramAPI\Exception\ThrottledException $e) {
						$get_from_follow=false;
                } catch ( \InstagramAPI\Exception\NotFoundException $e ) {
                $climate->error( '@' . $d['username'] . ' not found or maybe user blocked you (login to Instagram website or mobile app and check that).' );
                unset( $data_targ[ $key ] );
                continue;
                } catch ( Exception $e ) {
                throw $e;
                }
			
                // DEBUG - BEGIN
                //$followers_json = json_decode($followers);
                // $climate->out(var_dump($followers_json));
                // exit;
                // DEBUG - END
                if ( empty( $followers->getUsers() ) ) {
                $climate->error( '@' . $d['username'] . " don't have any follower." );
                unset( $data_targ[ $key ] );
                continue;
				}
				}
				
                //$climate->out(print_r($likers_list,true));
                $followers_ids = [];
				if (!$get_from_follow){
                foreach ($likers_list as $likers) {
                    //$climate->out($likers->getUsers());
                    foreach ($likers->getUsers() as $follower) {
                        /*foreach ($followers as $follower) {*/
                        $has_anonymous_profile_picture = $follower->getHasAnonymousProfilePicture();
                        $is_private        = $follower->getIsPrivate();
                        $is_verified       = $follower->getIsVerified();
                        $latest_reel_media = $follower->getLatestReelMedia();
                        if (!($is_private) && !($is_verified) && !($has_anonymous_profile_picture) && ($latest_reel_media !== "0")) {
                            if (!empty($fresh_stories_range)) {
                                $fresh_stories_min = time() - round($fresh_stories_range*60);
                                if ($latest_reel_media >= $fresh_stories_min) {
                                    $followers_ids[] = $follower->getPk();
                                }
                            } else {
                                // Check is latest
                                $followers_ids[] = $follower->getPk();
                            }
                        }
                        /*}*/
                    }
                }
				}else {
					$followers_ids = null;
                            unset($followers_ids);
                            $followers_ids = [];
					$follos = json_decode( $followers, true );
					$data_targ[ $key ]['max_id'] = $follos['next_max_id'];
                $followers_ids = [];
                foreach ( $followers->getUsers() as $follower ) {
                $is_private                    = $follower->getIsPrivate();
                $has_anonymous_profile_picture = $follower->getHasAnonymousProfilePicture();
                $is_verified                   = $follower->getIsVerified();
                // Check is user have stories at scrapping
                $latest_reel_media = $follower->getLatestReelMedia();
                if ( ! ( $is_private ) && ! ( $has_anonymous_profile_picture ) && ! ( $is_verified ) && ( null !== $latest_reel_media ) ) {
                // Mark as seen only fresh stories, which posted no more than X hours ago
                if ( isset( $fresh_stories_range ) ) {
                $fresh_stories_min = time() - round( $fresh_stories_range * 60 * 60 );
                if ( $latest_reel_media >= strtotime( $fresh_stories_min ) ) {
                $followers_ids[] = $follower->getPk();
                }
                } else {
                $followers_ids[] = $follower->getPk();
                }
                }
                }
                }

                if ($followers_ids) {
                    $followers_ids = array_unique($followers_ids);
                    $friendships = json_decode($ig->people->getFriendships($followers_ids));
                    foreach ($friendships->friendship_statuses as $friendship_key => $friendship) {
                        if ($friendship->following === false) {
                            $users_ids[] = $friendship_key;
                        }
                    }
                    if ($users_ids) {
                        $followers_ids = $users_ids;
                    }
                }
				  if ( count($followers_ids) == 0 ) {
                $climate->error( '@' . $d['username'] . " don't have any follower." );
                unset( $data_targ[ $key ] );
                continue;
				}
                // Re-indexing array
                $followers_ids = array_values($followers_ids);
                $data_targ[$key]['users_count'] = $d['users_count'] + count($followers_ids);
                $number = count($followers_ids);
                if ($is_gf_first) {
                    $climate->info($number . ' followers of @' . $d['username'] . ' collected.');
					
                    $is_gf_first = 0;
                } else {
                    $climate->info("Next " . $number . " followers with valid stories of @" . $d['username'] . " collected. Total: " . number_format($data_targ[$key]['users_count'], 0, '.', ' ') . " followers of @" . $d['username'] . " parsed.");
                }
                $index_new = 0;
                $index_old = 0;
                do {
                    $index_new += 20;
                    if (!isset($followers_ids[$index_new])) {
                        do {
                            $index_new -= 1;
                        } while (!isset($followers_ids[$index_new]));
                    }
                    if ($index_new < $index_old) {
                        break;
                    }
                    $ids = [];
                    if (file_exists(__DIR__ .'/'.$usfile)) {
                        if (filesize(__DIR__ .'/'.$usfile) > 0) {
                            $q_sids = file_get_contents(__DIR__ .'/'.$usfile);
                            $qisds_ = explode(',', $q_sids);
                        } else {
                            $qisds_ = null;
                        }
                    } else {
                        $userfile = fopen(__DIR__ .'/'.$usfile, 'w');
                        fwrite($userfile, '');
                        fclose($userfile);
                        $qisds_ = null;
                    }


                    for ($i = $index_old; $i <= $index_new; $i++) {
                        if (isset($followers_ids[$i])) {
                            if (!empty($qisds_) && in_array($followers_ids[$i], $qisds_)) {
                                continue;
                            } else {
                                $ids[] = $followers_ids[$i];
                            }
                        }
                    }
					

                    //$ci->out(print_r($qids_));
                 
                    try {
						
                        try {
							
                         $stories_reels = $ig->story->getReelsMediaFeedWeb($ids);
						 sleep(2);
						 } catch (\InstagramAPI\Exception\ThrottledException $e) {
                        $climate->darkGray('getReelsMediaFeedWeb Throttled! Resting during 30 minutes before try again.');
                        sleep(15*60);
                        } catch (\InstagramAPI\Exception\BadRequestException $e) {
                            // Invalid reel id list.
                            // That's mean that this users don't have stories.
                        } catch (Exception $e) {
                            throw $e;
                        }
                        $counter1 += 1;
                        
                                                $reels = json_decode($stories_reels);
                                                $reels = $reels->data->reels_media;

                                                foreach ($reels as $r) {
                                                    // $items = null;
                                                    // unset($items);
                                                    // $items = [];
                                                    // $items = $r->getItems();

                                                    // $stories_loop      = [];

                                                    $items        = [];
                                                    $stories_loop = [];
                
                                                    $items = $r->items;
                
                                                    $votableStoryUsers = false;

                                                    /**
                                                     * 
                                                     * 
                                                     * WE COLLECT VOTABLE STORIES HERE
                                                     * 
                                                     * 
                                                     */

                                                    foreach ($items as $item) {
                                                        if (count($item->tappable_objects) > 0) {
                                                            foreach ($item->tappable_objects as $object) {
                                                                if ($object->__typename === 'GraphTappableFallback') {
                                                                    $votableStoryUsers[] = $item->owner->id;
                                                                }
                                                            }
                                                        }
                
                                                        // Find the last story of the user - start
                                                        
                
                                                      
                                                    }
                                                
                                                     /**
                                                     * 
                                                     * 
                                                     * UNTIL HERE
                                                     * 
                                                     * 
                                                     */
              
                                                    if ($votableStoryUsers) {
                                        foreach ($votableStoryUsers as $userId) {
											try {
                                            $stories_reels = $ig->story->getUserStoryFeed($userId);
											
                                      
											 } catch (\InstagramAPI\Exception\BadRequestException $e) {
                                                            // Invalid reel id list or bad response 
                                                            throw $e;
                                                        } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                           $climate->darkGray('getUserStoryFeed Throttled! Resting during 15 minutes before try again.');
                                                            sleep(15*60);
                                                        }
                                            $itemsJson = json_decode($stories_reels);
                                            $items     = $stories_reels->getReel()->getItems();
                                            $reel      = $itemsJson->reel;
                                            $items     = $itemsJson->reel->items;

                                        
                                                foreach ($items as $item) {
                                                    if ($item->id) {

                                                    if (isset($item->story_polls) && !isset($item->story_polls[0]->poll_sticker->viewer_vote) && $item->can_reply && !$poll_throttled) {
                                                        if ($data->is_poll_vote_active) {
                                                        $poll_id  = $item->story_polls[0]->poll_sticker->poll_id;
                                                        $media_id = $item->id;
                                                        $option1  = $item->story_polls[0]->poll_sticker->tallies[0]->count;
                                                        $option2  = $item->story_polls[0]->poll_sticker->tallies[1]->count;
                                                        $vote     = $option1 > $option2 ? 0 : 1;
          
                                                        try {
                                                            $resp = $ig->story->votePollStory($media_id, $poll_id, $vote);
                                                            $response = json_decode($resp);
															
                                                        $now_ms = strtotime(date('Y-m-d H:i:s'));
                                            if ($now_ms - $begin_ms >= 15) {
                                                // all fine
                                            } else {
                                                $counter3 = 15 - ($now_ms - $begin_ms) + rand(1, 3);
                                                $climate->darkGray('Starting ' . $counter3 . ' second(s) delay for bypassing Instagram limits.');
                                                $vProgress = $climate->progress()->total($counter3);
                                                do {
                                                    $vProgress->advance(1, $counter3. 'second(s) left');
                                                    sleep(1);
                                                    $counter3 -= 1;
                                                } while (0 != $counter3);
                                            }
                                            $begin_ms = strtotime(date('Y-m-d H:i:s'));

                                                            if ($response->status == 'ok') {
                                                                $poll_votes_count++;
                                                                 $mycount++;

                                                                 $climate->magenta(date('H:i:s') .  ' - Poll Voted : ' . $vote . ' Votes Given: ' . $poll_votes_count . ' Total Actions : ' . $mycount);
                                                            } else {
                                                                $climate->magenta(date('H:i:s') .' - Fail to vote poll \n');
                                                               
                                                            }
                                                        } catch ( \InstagramAPI\Exception\BadRequestException $e ) {
                                                           
                                                            sleep(2);
                                                        } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                            $climate->magenta("Bypassing action limits...");
															$stories_loop[] = $item;

                                                              $poll_throttled = true;
                                                        } catch (\Exception $e) {
                                                           
                                                            //throw $e;
                                                            continue;
                                                        }

                                                    }
                                                }

                                                    if (isset($item->story_quizs) && !isset($item->story_quizs[0]->quiz_sticker->viewer_answer) && $item->can_reply && !$quiz_throttled) {
                                                        if ($data->is_quiz_answers_active) {
                                                        $quiz_id  = $item->story_quizs[0]->quiz_sticker->quiz_id;
                                                        $media_id = $item->pk;
                                                        $vote     = $item->story_quizs[0]->quiz_sticker->correct_answer;
                                                        try {
                                                            $resp = $ig->story->voteQuizStory($media_id, $quiz_id, $vote);
                                                            $response = json_decode($resp);
                                                            
                                                            if ($response->status == 'ok') {
                                                                $quiz_answers_count++;
                                                $mycount++;


                                                $climate->lightGray(date('H:i:s') . ' - Quiz Answered: ' . $vote . ' Quiz Answers Given: ' . $quiz_answers_count . ' Total Actions : ' . $mycount);
                                                            } else {
                                                                continue;
                                                            }
                                                        } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                            $climate->lightGray("Bypassing action limits...");
                                            sleep(1);
											$stories_loop[] = $item;

                                            $quiz_throttled = true;
                                                        } catch (\Exception $e) {
                                                           
                                                            continue;
                                                        }

                                                        
                                                    }
                                                }

                                                    if (isset($item->story_sliders) && !isset($item->story_sliders[0]->slider_sticker->viewer_vote) && !$slider_throttled) {
                                                            if ($data->is_slider_points_active) {
                                                        $slider_id = $item->story_sliders[0]->slider_sticker->slider_id;
                                                        $media_id  = $item->id;
                                                        $vote      = (mt_rand($data->slider_points_range[0], $data->slider_points_range[1]) / 100);
                                                        try {
                                                            $resp = $ig->story->voteSliderStory($media_id, $slider_id, $vote);
                                                            $response = json_decode($resp);
															  $now_ms = strtotime(date('Y-m-d H:i:s'));
                                            if ($now_ms - $begin_ms >= 15) {
                                                // all fine
                                            } else {
                                                $counter3 = 15 - ($now_ms - $begin_ms) + rand(1, 3);
                                                $climate->darkGray('Starting ' . $counter3 . ' second(s) delay for bypassing Instagram limits.');
                                                $vProgress = $climate->progress()->total($counter3);
                                                do {
                                                    $vProgress->advance(1, $counter3. 'second(s) left');
                                                    sleep(1);
                                                    $counter3 -= 1;
                                                } while (0 != $counter3);
                                            }
                                            $begin_ms = strtotime(date('Y-m-d H:i:s'));

                                                            if ($response->status == 'ok') {
                                                               $slider_points_count++;
                                                $mycount++;
												$point_prt= $vote * 100;

                                                $climate->blue(date('H:i:s') .  ' - Slider point given : %' . $point_prt . ' Given points: ' . $slider_points_count . ' Total Actions : ' . $mycount);

                                                            } else {
                                                               continue;
                                                            }
                                                        } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                             sleep(1);
                                            $slider_throttled = true;
											$stories_loop[] = $item;

                                            $climate->blue("Bypassing action limits...");
                                                        } catch ( \InstagramAPI\Exception\BadRequestException $e ) {
                                                          
                                                        } catch (\Exception $e) {
                                                           
                                                            continue;
                                                        }
                                           
                                                      
                                                    }
                                                }
                                           
											
										
													
											
                                                         
                                    
                                                    if (isset($item->story_questions) && $item->can_reply && !$question_throttled) {
                                                        if ($data->is_questions_answers_active) {
                                                        $question_id = $item->story_questions[0]->question_sticker->question_id;
                                                        $media_id    = $item->id;
                                                        $question    = $item->story_questions[0]->question_sticker->question;

                                       
                                       
                                        $questions = $data->questions_answers;
                                        $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
                                        $realfilename = $ig->account_id.'-qafile.txt';
                                        if (file_exists(__DIR__ .'/'.$realfilename)) {
                                            if (filesize(__DIR__ .'/'.$realfilename) > 0) {
                                                $q_ids = file_get_contents(__DIR__ .'/'.$realfilename);
                                                $qids_ = explode(',', $q_ids);
                                            } else {
                                                $qids_ = null;
                                            }
                                        } else {
                                            $question_answers_file = fopen(__DIR__ .'/'.$realfilename, 'w');
                                            fwrite($question_answers_file, '');
                                            fclose($question_answers_file);
                                            $qids_ = null;
                                        }

                                        if (!empty($qids_) && in_array($item->id, $qids_)) {
                                            continue;
                                        }


                                        try {
                                            $question_answers_file = fopen(__DIR__ .'/'.$realfilename, 'w');
                                            $resp = $ig->story->answerStoryQuestion($media_id, $question_id, $real_respond);
                                                                $response = json_decode($resp);
                                            $now_ms = strtotime(date('Y-m-d H:i:s'));
                                            if ($now_ms - $begin_ms >= 15) {
                                                // all fine
                                            } else {
                                                $counter3 = 15 - ($now_ms - $begin_ms) + rand(1, 3);
                                                $climate->darkGray('Starting ' . $counter3 . ' second(s) delay for bypassing Instagram limits.');
                                                $vProgress = $climate->progress()->total($counter3);
                                                do {
                                                    $vProgress->advance(1, $counter3. 'second(s) left');
                                                    sleep(1);
                                                    $counter3 -= 1;
                                                } while (0 != $counter3);
                                            }
                                            $begin_ms = strtotime(date('Y-m-d H:i:s'));
                                           if ($response->status == 'ok') {
                                                $mycount++;
                                                $question_answers_count++;
                                                $climate->yellow(date('H:i:s') .  ' - Question Answered: ' . $real_respond . ' Answers Given: ' . $question_answers_count . ' Total Actions : ' .$mycount);
                                                $qids_[] = $item->id;

                                                //$climate->out(print_r($qids_));
                                                fwrite($question_answers_file, join(',', $qids_));
                                                $ufl = fopen(__DIR__ .'/'.$usfile, 'w');
                                                $qisds_[] = $item->user->pk;
                                                //$ci->infoBold(print_r($qids_));
                                                fwrite($ufl, join(',', $qisds_));
                                            }
                                        } catch (\InstagramAPI\Exception\BadRequestException $e) {
                                        } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                            sleep(1);
                                            $climate->yellow("Bypassing action limits...");
											$stories_loop[] = $item;

                                            $question_throttled = true;
                                        } catch (\Exception $e) {
                                            continue;
                                        }
                                    }
                                }
                                       if ($slider_throttled) {
                                        $stories_loop[] = $item;
                                        continue;
                                    } if ($question_throttled) {
                                        $stories_loop[] = $item;
                                        continue;
                                    } if ($poll_throttled) {
                                        $stories_loop[] = $item;
                                        continue;
                                    } if ($quiz_throttled) {
                                        $stories_loop[] = $item;
                                        continue;
                                    }
                                     else {
                                        continue;
                                    }
                                    	
									 if ($data->is_mass_story_vivew_active) {
                                    if (empty($stories)) {
                                        $stories = $stories_loop;
                                    } else {
                                        $stories = array_merge($stories, $stories_loop);
                                    }

                                    $st_count =  $st_count + count($stories_loop);
                                    $view_count = $view_count + count($stories_loop);
                                    if ($st_count >= 10) {
                                        // $climate->out($st_count . " stories found. / Debug: getReelsMediaFeed (" . $counter1 . "), markMediaSeen (" . $counter2 . ")");
                                        /// $climate->out($st_count . " stories found.");
                                        $now_ms = strtotime(date('Y-m-d H:i:s'));
                                        if ($now_ms - $begin_ms >= 60) {
                                            // all fine
                                        } else {
                                            $counter3 = 60 - ($now_ms - $begin_ms) + rand(1, 3);
                                            $climate->darkGray('Starting ' . $counter3 . ' second(s) delay for bypassing Instagram limits.');
                                            $vProgress = $climate->progress()->total($counter3);
                                            do {
                                                $vProgress->advance(1, $counter3. 'second(s) left');
                                                sleep(1);
                                                $counter3 -= 1;
                                            } while (0 != $counter3);
                                        }
                                        // Mark media seen sections
                                        $is_connected = false;
                                        $is_connected_count = 0;
                                        $fail_message = "We couldn't establish connection with Instagram 7 times. Please try again later.";
                                        do {
                                            if (7 == $is_connected_count) {
                                                if ($e) {
                                                    $climate->darkGray('RESPONSE' . $e->getMessage() . ' ');
                                                }
                                                throw new Exception($fail_message);
                                            }
                                            // Mark collected stories as seen
                                            // Connection break adaptation for mobile proxies
                                            try {
                                                $is_connected = true;

                                                $st_count_seen = number_format($st_count, 0, '.', ' ');
                                                $mycount++;
                                                $climate->blueBold($st_count_seen . ' stories marked as seen. Total Seen : ' . $view_count . ' Total Actions: ' . $mycount);
                                            } catch (\InstagramAPI\Exception\NetworkException $e) {
                                                sleep(3);
                                            } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                                                sleep(3);
                                            } catch (\InstagramAPI\Exception\InstagramException $e) {
                                                throw $e;
                                            } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                $climate->error("Bypassing action limits...");
                                                sleep(2);
                                                $mass_view_throttled = true;
                                            } catch (Exception $e) {
                                                throw $e;
                                            }
                                            $is_connected_count += 1;
                                        } while (!$is_connected);
                                        $begin_ms = strtotime(date('Y-m-d H:i:s'));
                                        $st_count = 0;
                                        $stories_loop = [];
                                    }
                                }
                                $stories_loop = [];
												
                               
                                   
                                /*$latest_reel_media = $r->getLatestReelMedia();
                                $seen = $r->getSeen();*/
                                /*if (("0" !== $latest_reel_media)) {
                                    foreach ($items as $it) {
                                        if (($it->getTakenAt() == $latest_reel_media) && ($it->getId()) !== "0") {
                                            $stories_loop[] = $it;
                                        }
                                    }
                                }*/
                               






                            }
                               

														  }
													}
													
									 }
                                                }
											
								$throttled_windows = rand(1800, 3600);
											if (time() - $last_throttled_follow >= $throttled_windows) {
                                    $get_from_follow=false;
                                    $last_throttled_follow = time();
                                }		 
								$throttled_window = rand(240, 360);
                                if (time() - $last_throttled_poll >= $throttled_window) {
                                    $poll_throttled = false;
                                    $last_throttled_poll = time();
                                }
                                if (time() - $last_throttled_quiz >= $throttled_window) {
                                    $quiz_throttled = false;
                                    $last_throttled_quiz = time();
                                }
                                if (time() - $last_throttled_slider >= $throttled_window) {
                                    $slider_throttled = false;
                                    $last_throttled_slider = time();
                                }
                                if (time() - $last_throttled_question >= $throttled_window) {
                                    $question_throttled = false;
                                    $last_throttled_question = time();
                                }
                                if (time() - $last_throttled_countdown >= $throttled_window) {
                                    $countdown_throttled = false;
                                    $last_throttled_countdown = time();
                                }
                                if (time() - $last_throttled_mass_view >= $throttled_window) {
                                    $mass_view_throttled = false;
                                    $last_throttled_mass_view = time();
                                }
                                   $now = strtotime(date('Y-m-d H:i:s'));
                                if ($now - $begin > 299) {
                                    $begin = strtotime(date('Y-m-d H:i:s'));
                                    $delitel = $delitel + 1;
                                    $speed = (int) ($mycount * 12 * 24 / $delitel);
                                    $climate->out('');
                                    $climate->out('Estimated speed is ' . number_format($speed, 0, '.', ' ') . ' react/day.');
                                    $climate->out('© Hypervote Terminal. Always use Hypervote from our official source, downloading other (nulled) versions could end up losing your account.');
                                    $climate->out('');
                                }
                                $now_f = strtotime(date('Y-m-d H:i:s'));
                                if ($now_f - $begin_f > 1) {
                                    $begin_f = strtotime(date('Y-m-d H:i:s'));
                                    // $climate->out($st_count . " stories found. / Debug: getReelsMediaFeed (" . $counter1 . "), markMediaSeen (" . $counter2 . ")");
                                        //$climate->out($st_count . " stories found.");
                                }
       
                     
						
                    } catch (\InstagramAPI\Exception\NetworkException $e) {
                        $climate->error("We couldn't connect to Instagram at the moment. Trying again.");
                        sleep(10);
                        $index_new -= 10;
                        continue;
                    } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                        $climate->error('Instagram sent us empty response. Trying again.');
                        sleep(10);
                        $index_new -= 10;
                        continue;
                    } catch (\InstagramAPI\Exception\LoginRequiredException $e) {
                        $climate->error('Please login again to your Instagram account. Login required.');
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
                        $climate->error('Please login again and pass verification challenge. Instagram will send you a security code to verify your identity.');
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                        $climate->error('Please go to Instagram website or mobile app and pass checkpoint!');
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\AccountDisabledException $e) {
                        $climate->error('Your account has been disabled for violating Instagram terms. Go Instagram website or mobile app to learn how you may be able to restore your account.');
                        $climate->error('Use this form for recovery your account: https://help.instagram.com/contact/1652567838289083');
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\ConsentRequiredException $e) {
                        $climate->error('Instagram updated Terms and Data Policy. Please go to Instagram website or mobile app to review these changes and accept them.');
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\SentryBlockException $e) {
                        $climate->error('Access to Instagram API restricted for spam behavior or otherwise abusing.');
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\ThrottledException $e) {
                        $climate->error('Throttled by Instagram because of too many API requests.');
                        $climate->error('12 hours rest for account started because you reached Instagram daily limit for Hypervote.');
                        sleep(43200);
                    } catch (Exception $e) {
                        $climate->error($e->getMessage());
                        sleep(7);
                    }
                    $index_old = $index_new + 1;
                } while (null != $data_targ[$key]['max_id']);
                // Check is $max_id is null
                if (null == $data_targ[$key]['max_id']) {
                    $climate->blue('All stories of @' . $d['username'] . "'s followers successfully Voted.");
                    unset($data_targ[$key]);
                    continue;
                }
                /*if($view_count >= 14900){
            $generated_password = randomPassword();
            $change_password = $ig->account->changePassword($password,$generated_password);
            $climate->out("New Password: ".$generated_password);
            }*/
            } catch (\InstagramAPI\Exception\NetworkException $e) {
                sleep(7);
            } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                sleep(7);
            } catch (\InstagramAPI\Exception\LoginRequiredException $e) {
                $climate->error('Please login again to your Instagram account. Login required.');
                run($ig, $climate);
            } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
                $climate->error('Please login again and pass verification challenge. Instagram will send you a security code to verify your identity.');
                run($ig, $climate);
            } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                $climate->error('Please go to Instagram website or mobile app and pass checkpoint!');
                run($ig, $climate);
            } catch (\InstagramAPI\Exception\AccountDisabledException $e) {
                $climate->error('Your account has been disabled for violating Instagram terms. Go Instagram website or mobile app to learn how you may be able to restore your account.');
                $climate->error('Use this form for recovery your account: https://help.instagram.com/contact/1652567838289083');
                run($ig, $climate);
            } catch (\InstagramAPI\Exception\ConsentRequiredException $e) {
                $climate->error('Instagram updated Terms and Data Policy. Please go to Instagram website or mobile app to review these changes and accept them.');
                run($ig, $climate);
            } catch (\InstagramAPI\Exception\SentryBlockException $e) {
                $climate->error('Access to Instagram API restricted for spam behavior or otherwise abusing. ');
                $climate->error('6 hours rest for account started because you reached Instagram daily limit for Hypervote.');
                sleep(21200);
            } catch (\InstagramAPI\Exception\ThrottledException $e) {
                $climate->error('Throttled by Instagram because of too many API requests.');
                $climate->error('12 hours rest for account started because you reached Instagram daily limit for Hypervote.');
                sleep(43200);
            } catch (Exception $e) {
                $climate->errorBold($e->getMessage());
                sleep(7);
            }
        }
    } while (!empty($data_targ));
    $climate->blue('All stories related to the targets seen. Starting the new loop.');
    $climate->blue('');
    hypervote_v1($data, $targets, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy);
}
/**
 * Send request
 * @param $url
 * @return mixed
 */
function request($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
}