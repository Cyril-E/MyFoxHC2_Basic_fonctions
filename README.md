MyFoxHC2_Basic_fonctions
========================

Fonction de base avec l'API MyFox pour centrale HC2

Pour connaître l’état de votre alarme :  http://xxxxx/myfox.php  le script vous retournera alors sous la forme XML l’état de l’alarme sous forme numérique (statusvalue) (1:Protection  Désactivé, 2:Protection  Partielle, 4: Protection Totale,  ) ou texte (statuslabel)   armed partial disarmed

Pour changer l’état de l’alarme http://xxxxx/myFox.php?levelrequest=armed          partial   ou   disarmed

Lecture de la valeur d'une sonde de temperature
