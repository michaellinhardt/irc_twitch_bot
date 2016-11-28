<?php

define( 'PERMIN_XP', 10 );
define( 'PERMIN_GILS', 1 );

define( 'PATH_FROM_VARS', 'C:/Nestbot/scripts/vars.ini' );
define( 'PATH_FROM_ONLINE', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/from/online.txt' );
define( 'PATH_FROM_TOKEN_FOLLOW', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/from/tokenFollow.txt' );
define( 'PATH_FROM_EVENT', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/from/event.txt' );
define( 'PATH_FROM_KILL', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/from/kill.txt' );
define( 'PATH_FROM_QUIZZ', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/from/quizz.txt' );
define( 'PATH_FROM_DISLIKE', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/from/dislike.txt' );
define( 'PATH_FROM_CMD', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/from/cmd.txt' );
define( 'PATH_FROM_MSG', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/from/msg.txt' );

define( 'PATH_OVERLAY_TOKEN_TOPLIST', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/tokenList.txt' );
define( 'PATH_OVERLAY_LAYOUT', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/layout.txt') ;
define( 'PATH_OVERLAY_NEWFOLLOWER', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/newFollower2.txt');
define( 'PATH_OVERLAY_COUNT_FOLLOWER', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/countFollower.txt');

define( 'PATH_OVERLAY_MICRO_ITEM', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/microItem.txt');
define( 'PATH_OVERLAY_COUNT_SET', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/countSet.txt');
define( 'PATH_OVERLAY_COUNT_LEGS', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/countLegs.txt');

define( 'PATH_OVERLAY_H_XP', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/HXP.txt');
define( 'PATH_OVERLAY_H_LEGS', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/HLegs.txt');
define( 'PATH_OVERLAY_H_SET', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/HSet.txt');

define( 'PATH_OVERLAY_OUTILS_COUNT', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/outilsCount.txt');

define ('PATH_OVERLAY_MSG_MSG', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/msg_msg.txt' ) ;
define ('PATH_OVERLAY_MSG_TITRE', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/msg_titre.txt' ) ;

define( 'PATH_TO_DIRE', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/to/dire.txt' );
define( 'PATH_TO_EXEC', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/to/exec.txt' );
define( 'PATH_TO_RAFFLE', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/mirc/to/raffle.txt' );

define( 'PATH_QUIZZ_FILE', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/quizz') ;

define( 'PATH_OVERLAY_YOUTUBE_ADD', 'C:/wamp/www/nesstream/overlay/youtubeAdd.txt') ;
define( 'PATH_OVERLAY_YOUTUBE_STATUS', 'C:/wamp/www/nesstream/overlay/youtubeStatus.txt') ;
define( 'PATH_OVERLAY_YOUTUBE', 'C:/Users/Nestoyeur/Google Drive/www/nesstream/overlay/youtube.txt' );

define('EVENT_STEP_AFTER_PVE', 'QUIZZ'); // par defaut QUIZZ sinon PVE

define( 'COOLDOWN_PVE', 25) ; // en minute, temps entre chaque proc
define( 'COOLDOWN_QUIZZ', 25); // en minute, temps entre chaque proc
define( 'COOLDOWN_PVE_LIFETIME', 10 ) ; // en minute, dur� du proc si mob pas tu�
define( 'COOLDOWN_QUIZZ_LIFETIME', 10 ) ; // en minute, dur� du proc si mob pas tu�
define( 'COOLDOWN_MSG_YOUTUBE_LIFETIME', 150); // en seconde, dur�e d'un titre fb
define( 'COOLDOWN_MSG_LIFETIME', 120 ); // en seconde, dur�e d'un message random

define( 'COOLDOWN_VIEWER_MSG_LIFETIME', 5 ); // en minutes, dur�e d'un message viewer (pay�)
define( 'VIEWER_MSG_PRICE', 300 ); // en gils
define ('DISLIKE_GILS', 100);

define( 'NEW_FOLLOW_LIMIT', 15) ;
define( 'OLD_FOLLOW_LIMIT', 15) ;
define( 'NEW_FOLLOWER_DIRE_TIMER', 35 ); // en seconde, le temps a attendre quand la roue tourne
define( 'QUIZZ_DIRE_TIMER', 120 ); // en seconde, le temps avant que mirc annonce le quizz

define( 'PVE_ATK_XP', 0.2);
define( 'PVE_ATK_GILS', 100);
define( 'QUIZZ_REP_XP', 0.3);
define( 'QUIZZ_REP_GILS', 200);
define( 'QUIZZ_AUTEUR_XP', 0.1);
define( 'QUIZZ_AUTEUR_GILS', 50);

define( 'TWITCH_CHAN', 'nestoyeur') ;

define ('MSG_QUIZZ_TITRE', '[QUIZZ] ') ;
define ('MSG_PVE_TITRE', '[PVE] ');
define ('MSG_YOUTUBE_TITRE', '[SONG] ');

$aPrice[0] = '40 level instantané' ;
$aPrice[1] = '20 level instantané' ;
$aPrice[2] = '10 level instantané' ;
$aPrice[3] = 'GILS fois 2, ce bonus est valable durant 1 semaine 24h/24h' ;
$aPrice[4] = '4000 gils' ;
$aPrice[5] = '2000 gils' ;
$aPrice[6] = '1000 gils' ;
$aPrice[7] = 'XP fois 2, ce bonus est valable durant 1 semaine 24h/24h' ;