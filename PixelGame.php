<?php
//================================
// PHP PixelGame By BOUNITO 2011
//================================
session_start();

function clean_string($entree)
	{
	$sortie = @strip_tags($entree);			//Supression de Tags HTML ou PHP
	$sortie = str_replace(";",",",$sortie);		//Remplacement des ; par des ,
	$sortie = str_replace(chr(10)," ",$sortie);	//Remplacement des retours chariot par des espaces
	return ($sortie);
	}

//----------------------------------------------------------------Connexion
require 'params.php';
$connexion = mysql_connect($db_host,$db_user,$db_pwd);
if (!$connexion) {echo "Impossible d'effectuer la connexion";exit;}
$db = mysql_select_db($db_name, $connexion);
if (!$db) {echo "Impossible de sélectionner cette base données";exit;}

// réinitialisation de toutes les variables pour une nouvelle partie
function init()
	{
	unset($_SESSION['tab']);
	unset($tab);
	unset($_SESSION['score']);
	unset($score);
	unset($_SESSION['score_max']);
	unset($score_max);
	unset($_SESSION['c_old']);
	unset($c_old);
	}

	
//========================================================
//Gestion du nombre de colonne/lignes/taille
//
//******** Tailles prédéfinies
if (isset($_GET['t1']) || isset($_GET['t2']) || isset($_GET['t3']))
	{
	if (isset($_GET['t1']))
		{
		$i_max=9;
		$j_max=4;
		$carre_size=87;
		}
	if (isset($_GET['t2']))
		{
		$i_max=16;
		$j_max=7;
		$carre_size=50;
		}
	if (isset($_GET['t3']))
		{
		$i_max=32;
		$j_max=14;
		$carre_size=25;
		}
	
	$_SESSION['i_max']=$i_max;
	$_SESSION['j_max']=$j_max;
	$_SESSION['carre_size']=$carre_size;
	init();
	}
//******** 
if (isset($_SESSION['i_max']))
	{
	$i_max = $_SESSION['i_max'];
	if (isset($_GET['i_d']) && $i_max>1)
		{
		$i_max--;
		init();
		}
	if (isset($_GET['i_u']))
		{
		init();
		$i_max++;
		
		}
	}
else
	{
	$i_max = 16;
	}
$_SESSION['i_max']=$i_max;
//--------------------------
if (isset($_SESSION['j_max']))
	{
	$j_max = $_SESSION['j_max'];
	if (isset($_GET['j_d']) && $j_max>1)
		{
		$j_max--;
		init();		}
	if (isset($_GET['j_u']))
		{
		$j_max++;
		init();
		}
	}
else
	{
	$j_max = 7;
	}
$_SESSION['j_max']=$j_max;
//--------------------------
if (isset($_SESSION['carre_size']))
	{
	$carre_size = $_SESSION['carre_size'];
	if (isset($_GET['cs_d']) && $carre_size>4)
		$carre_size=$carre_size-2;
	if (isset($_GET['cs_u']))
		$carre_size=$carre_size+2;
	}
else
	$carre_size = 50;
$_SESSION['carre_size']=$carre_size;
//--------------------------

if (isset($_SESSION['tab'])) $tab=$_SESSION['tab'];
else unset($tab);
//print_r($tab);

if (isset($_SESSION['score'])) $score=$_SESSION['score'];

if (isset($_GET['c_new'])) $c_new=$_GET['c_new'];

if (isset($_SESSION['c_old']))
	{
	$c_old=$_SESSION['c_old'];
	if ($c_old<>$c_new) $score++;
	}

if (isset($_SESSION['score_max'])) $score_max=$_SESSION['score_max'];
else $score_max=999;

//*********************************************************************************
//Sauvegarde du score :
if (isset($_POST['pseudo']) && isset($_POST['score']))
	{
	$pseudo = clean_string($pseudo);
	$MaMachine = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
	$MonIp = $_SERVER["REMOTE_ADDR"];
	
	$req_ins =  "insert into PixelScore (p_pseudo,p_score,p_cols,p_lines,p_machine,p_ip) ".
  			"values ('$pseudo','".$_POST['score']."','$i_max','$j_max','$MaMachine','$MonIp')";
			
	//echo "<BR>".$req_ins."<BR>";
			
	$res_ins = mysql_query($req_ins);
	if (!$res_ins)
		die('Insertion invalide (proposition existante ?) : ' . mysql_error());
	else
		echo "<FONT color=red><B>Merci ".$pseudo." ! Ton score est sauvegardé.</B></FONT>";
	}
//*********************************************************************************


$col[0] = "grey";
$col[1] = "blue";
$col[2] = "red";
$col[3] = "green";
$col[4] = "yellow";
$col[5] = "orange";
//$col[5] = "dark orange";
//$col[5] = "gold";
$col[6] = "purple";


//echo "<BR>c_new=".$c_new;
//echo "<BR>c_old=".$c_old;


?>


<HTML>
<HEAD>
<TITLE>Bounito PixelGame</TITLE>
</HEAD>
<BODY>
<CENTER>
<?php

//Génération du tableau
if (!isset($tab))
	{
	//echo "<BR>Génération du tableau";
	for ($j=1;$j<=$j_max;$j++)
		for ($i=1;$i<=$i_max;$i++)
			$tab[$i][$j] = rand(1,6);
	//print_r($tab);
	$c_old = $tab[1][1];
	$c_new = $tab[1][1];
	$_SESSION['c_new'] = $c_new;
	$tab[1][1] = 0;
	$_SESSION['tab'] = $tab;
	$_SESSION['score'] = 0;
	}
//else
//	echo "Score : ".$score." coups (Meilleur : ".$score_max." coups)";
	
//Mise à jour du tableau
if (isset($tab))
	{
	//echo "<BR>Traitement du tableau : c_new=".$c_new;
	$modif = 1;
	$compteur=0;
	while ($modif<>0 && $compteur<($i_max*$j_max))
		{
		$modif=0;
		$compteur++;
		for ($j=1;$j<=$j_max;$j++)
			for ($i=1;$i<=$i_max;$i++)
				if ($tab[$i][$j]==0)
					{
					if ($tab[$i+1][$j]==$c_new)
						{
						$tab[$i+1][$j]=0;
						$modif++;
						}
					if ($tab[$i-1][$j]==$c_new)
						{
						$tab[$i-1][$j]=0;
						$modif++;
						}
					if ($tab[$i][$j+1]==$c_new)
						{
						$tab[$i][$j+1]=0;
						$modif++;
						}
					if ($tab[$i][$j-1]==$c_new)
						{
						$tab[$i][$j-1]=0;
						$modif++;
						}
					//echo "<BR>".$compteur." - ".$i.":".$j."-".$modif;
					}
		}
	$_SESSION['tab'] = $tab;
	}

//Gagné ?
if (isset($tab))
	{
	//echo "<BR>Combien de case reste-t-il ?   ";
	$reste = 0;
	for ($j=1;$j<=$j_max;$j++)
		for ($i=1;$i<=$i_max;$i++)
			if ($tab[$i][$j]<>0) $reste++;
			
	if ($reste==0)
		{
		echo "<H1>Congratulations, you succeed in ".$score." steps !</H1>";
		
		echo "<FORM action='index.php' method='POST'>
			Your name :<INPUT NAME='pseudo' VALUE='' SIZE=15 maxlength=50>
			<INPUT type=hidden VALUE='".$score."' NAME='score'>
			<INPUT type=submit VALUE='Save my name & play again !'>
			</FORM>";
		
		unset($_SESSION['tab']);
		unset($_SESSION['score']);
		unset($_SESSION['c_old']);
		if ($score<$score_max)
			{
			$score_max = $score;
			$_SESSION['score_max'] = $score_max;
			}
	
  $query = "select * from PixelScore";
  $result = mysql_query($query);
  if (!$result) die('Requête : '.$query.' invalide : ' . mysql_error());
  echo "Score Table :";
  echo "<TABLE BORDER=1><TR><TH>Date</TH><TH>Name</TH><TH>Score</TH><TH>Cols</TH><TH>Lines</TH><TH>Machine</TH><TH>IP</TH></TR>";
  
  while($row = mysql_fetch_row($result))
	{
	echo "<TR><TD>".$row[0]."</TD><TD>".$row[1]."</TD><TD>".$row[2]."</TD><TD>".$row[3]."</TD><TD>".$row[4]."</TD><TD>".$row[5]."</TD><TD>".$row[6]."</TD></TR>";
	}
  echo	"</TABLE>";	
		
		
		//echo "<A HREF='index.php'><H1>New Game partie</H1></A> <BR><BR><BR><BR><BR><BR><BR>";
		}
	else
		{
		//echo "<BR>Il reste encore ".$reste." cases...";
		
		//=============================================================================
		//Affichage du tableau
		if (!isset($c_new)) $c_new=$_SESSION['c_new'];
		
		echo "\n<TABLE CELLSPACING=0 CELLPADDING=0>";
		for ($j=1;$j<=$j_max;$j++)
			{
			echo "\n<TR>";
			for ($i=1;$i<=$i_max;$i++)
				{
				if ($tab[$i][$j]<>0)
					echo "<TD BGCOLOR='".$col[$tab[$i][$j]]."' WIDTH=".$carre_size." HEIGHT=".$carre_size.">&nbsp;</TD>";
				else
					echo "<TD BGCOLOR='".$col[$c_new]."' WIDTH=".$carre_size." HEIGHT=".$carre_size.">&nbsp;</TD>";
				}
			echo "\n</TR>";
			}
		echo "\n</TABLE>";

		//Affichage des boutons
		echo "<TABLE CELLSPACING=20 CELLPADDING=20 WIDTH=".($i_max*$carre_size)." BORDER=0>";
		echo "<TR>";

		$_SESSION['c_old']=$c_new;
		//$col[$c_new] = "grey";

		for ($cc=1;$cc<7;$cc++)
			if ($cc<>$c_new)
				echo "\n<TD BGCOLOR='".$col[$cc]."' onMouseOver=\"document.body.style.cursor='hand';\" onMouseOut=\"document.body.style.cursor='default';\" onClick=\"window.location.href = 'index.php?c_new=".$cc."';return false;\">&nbsp;</TD>";
			else
				echo "\n<TD BGCOLOR='".$col[$cc]."'>&nbsp;</TD>";

		echo "</TR>";
		echo "</TABLE>";
		}
	}

if (isset($score))
	echo "Score : ".$score." steps (Best : ".$score_max." steps)";
	
//Formulaire
//u=up et d=down	
?>
<BR><BR>
<B>Options</B> : 
<FORM action="index.php" method="GET">
<BR><INPUT type=submit name="t1" VALUE="Easy">&nbsp;<INPUT type=submit name="t2" VALUE="Normal">&nbsp;<INPUT type=submit name="t3" VALUE="Expert">
<BR>Custom
<BR><INPUT type=submit name="i_d" VALUE="-"> <?=$i_max?> cols <INPUT type=submit name="i_u" VALUE="+">
<BR><INPUT type=submit name="j_d" VALUE="-"> <?=$j_max?> lines <INPUT type=submit name="j_u" VALUE="+">
<BR><INPUT type=submit name="cs_d" VALUE="-" SELECTED=TRUE> Size (<?=$carre_size?> pixels) <INPUT type=submit name="cs_u" VALUE="+">
</FORM>

<A href='../posts.php' target=_blank>Write me a comment !</A>
          
</CENTER>
</BODY>