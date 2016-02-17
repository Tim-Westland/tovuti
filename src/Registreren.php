<?php
//init fields
$Voornaam = $Achternaam = $Adres = $Postcode = $Woonplaats = $Telefoon = $Email = $Gebruikersnaam = $Wachtwoord = $RetypeWachtwoord = NULL;

//init error fields
$FnameErr = $LnameErr = $ZipErr = $WoonplaatsErr = $TelErr = $MailErr = $UserErr = $PassErr = $RePassErr = NULL;

if(isset($_POST['Registreren']))
{
	$CheckOnErrors = false; // hulpvariabele voor het valideren van het formulier


	$Voornaam = $_POST["Voornaam"];
	$Achternaam = $_POST["Achternaam"];
	$Adres = $_POST["Adres"];
	$Postcode=$_POST['Postcode'];
	$Woonplaats=$_POST['Woonplaats'];
	$Telefoon=$_POST['Telefoon'];
	$Email=$_POST['Email'];
	$Gebruikersnaam=$_POST['Gebruikersnaam'];
	$Wachtwoord=$_POST['Wachtwoord'];
	$RetypeWachtwoord=$_POST['RetypeWachtwoord'];

	//BEGIN CONTROLES


	//controleer het voornaam veld
	if(!is_minlength($Voornaam, 2))
	{
	$FnameErr = 'Voornaam moet uit minimaal 2 tekens bestaan';
	$CheckOnErrors = true;
	}
	if(!is_Char_Only($Voornaam))
	{
	$FnameErr = 'Alleen letters zijn toegestaan';
	$CheckOnErrors = true;
	}
	if ($Voornaam == null)
	{
	$FnameErr = 'Voornaam is verplicht!';
	$CheckOnErrors = true;
	}
	//controleer het achternaam veld
	if(!is_minlength($Achternaam, 2))
	{
	$LnameErr = 'Voornaam moet uit minimaal 2 tekens bestaan';
	$CheckOnErrors = true;
	}
	if(!is_Char_Only($Achternaam))
	{
	$LnameErr = 'Alleen letters zijn toegestaan';
	$CheckOnErrors = true;
	}
	if ($Achternaam == null)
	{
	$LnameErr = 'Achternaam is verplicht!';
	$CheckOnErrors = true;
	}
	
	//controleer het postcode veld	
	if(!is_NL_PostalCode($Postcode))
	{
	$ZipErr = 'Postcode incorrect';
	$CheckOnErrors = true;
	}

	//controleer het plaats veld
	if(!is_Char_Only($Woonplaats))
	{
	$WoonplaatsErr = 'Alleen letters zijn toegestaan';
	$CheckOnErrors = true;
	}

	//controleer het telnr veld
	if(!is_NL_Telnr($Telefoon))
	{
	$TelErr = 'Telefoonnummer incorrect';
	$CheckOnErrors = true;
	}
	if ($Telefoon == null)
	{
	$TelErr = 'Telefoonnummer is verplicht!';
	$CheckOnErrors = true;
	}
	
	//controleer het email veld
	if(!is_email($Email))
	{
	$MailErr = 'Email incorrect';
	$CheckOnErrors = true;
	}
	if ($Email == null)
	{
	$MailErr = 'Email is verplicht!';
	$CheckOnErrors = true;
	}

	//controleer het username veld
	if(!is_Gebruikersnaam_Unique($Gebruikersnaam, $pdo))
	{
	$UserErr = 'Gebruikersnaam is al in gebruik';
	$CheckOnErrors = true;
	}
	if ($Gebruikersnaam == null)
	{
	$UserErr = 'Gebruikersnaam is verplicht!';
	$CheckOnErrors = true;
	}
	
	//controleer het paswoord veld
	if(!is_minlength($Wachtwoord, 6))
	{
	$PassErr = 'Wachtwoord moet uit minimaal 6 tekens bestaan';
	$CheckOnErrors = true;
	}
	if ($Wachtwoord == null)
	{
	$PassErr = 'Wachtwoord is verplicht!';
	$CheckOnErrors = true;
	}
	
	//controleer het retype paswoord veld
	if($Wachtwoord != $RetypeWachtwoord)
	{
	$RePassErr = 'Wachtwoord niet hetzelfde';
	$CheckOnErrors = true;
	}
	if ($RetypeWachtwoord == null)
	{
	$RePassErr = 'Wachtwoord is verplicht!';
	$CheckOnErrors = true;
	}

	if($CheckOnErrors == true) //aanvullen
	{
	require('./Forms/RegistrerenForm.php');
	}
	else
	{
		//formulier is succesvol gevalideerd

		//maak unieke salt
		$Salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));

		//hash het paswoord met de Salt
		$Wachtwoord = hash('sha512', $Wachtwoord . $Salt);

		
		$parameters = array(':Gebruikersnaam'=>$Gebruikersnaam,
							':Paswoord'=>$Wachtwoord,
							':Salt'=>$Salt,
							':Level'=>$Level);
		$sth = $pdo->prepare('INSERT INTO klanten (Gebruikersnaam, Wachtwoord, Salt, Level) VALUES (:Gebruikersnaam, :Wachtwoord, :Salt, :Level)');
		$sth->execute($parameters);
		$LastID = $pdo->lastInsertId();
		
		$parameters = array(':PersoonsID'=>$LastID,
							':Voornaam'=>$Voornaam,
							':Achternaam'=>$Achternaam,
							':Adres'=>$Adres,
							':Postcode'=>$Postcode,
							':Plaats'=>$Woonplaats,
							':Email'=>$Email,
							':Telefoon'=>$Telefoon);
		$sth = $pdo->prepare('INSERT INTO klanten (PersoonsID, Voornaam, Achternaam, Adres, Postcode, Plaats, Email, Telefoon) VALUES (:Voornaam, :Achternaam, :Adres, :Postcode, :Plaats, :Email, :Telefoon)');
		$sth->execute($parameters);				
		
		
		echo 'U heeft zich succesvol geregistreerd en kunt vanaf nu inloggen op de website';
		RedirectNaarPagina();
	}
}
else
{
	require('./Forms/RegistrerenForm.php');
}
?>




	