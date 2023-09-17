<?php 
	// Output Buffering (Ya3ny Takhzeen .. Fa Haykhzen El Data El Awl except 'headers') Start. For not sending outputs before headers.
	// It's preferable to set the 'ob_start()' before the 'session()' function.
	ob_start('ob_gzhandler'); // gz is a technique to handle the outputs and compress it to speed up the preformance
	session_start();
	$pageTitle = "Login";
	if(isset($_SESSION['user_front'])) {
		header('Location: profile.php');
	}
	include "init.php";

	if($_SERVER['REQUEST_METHOD'] == 'POST') {

		if(isset($_POST['submit_login'])){ // if data is coming from login form
			$user_front = htmlspecialchars($_POST['username'], ENT_QUOTES);
			$pass 		= $_POST['pass'];
			$hashedPass = sha1($pass);

			// Check if the user exist in database
			$stmt = $conn -> prepare("SELECT ID, Username, Password FROM users WHERE Username = ? AND Password = ?");
			$stmt -> execute(array($user_front, $hashedPass));
			$user_info = $stmt->fetch();
			$count = $stmt -> rowCount();

			// Check if there is a record with those information
			if($count > 0) {
				$_SESSION['user_front'] = $user_front;
				$_SESSION['user_front_id'] = $user_info['ID'];

				header('Location: profile.php');
				exit();
			}
		}else{ 						// if data is coming from signup form
			
			$form_errors = array();

			// making filter to the username 'written by the user', just in case the user tried to input any harmful <script>
			// so here we convert any username 'written by the user' to string, by converting any html special chars (<>/""''.....) to entities
			// which means it has no series effect in the code, it just got converted to string
			$username 	 = htmlspecialchars($_POST['username'], ENT_QUOTES);
			$email 	 	 = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			$pass 	 	 = $_POST['pass'];
			$pass2 	 	 = $_POST['pass2'];

			// ---------- start appending errors to $form_errors array ----------
			if(isset($username)){

				if(strlen($username) < 4){ $form_errors[] = 'Username can\'t be less than 4 characters'; }
			}
			if(isset($email)){
					if(filter_var($email, FILTER_VALIDATE_EMAIL) != true){ $form_errors[] = 'Email is not valid'; }
			}
			if(isset($pass)){
				if(!empty($pass)){
					if(sha1($pass) !== sha1($pass2)){ $form_errors[] = 'Password dosn\'t match'; }
				}else{ $form_errors[] = 'Password is empty'; }
			}
			// ---------- end appending errors to $form_errors array ----------

			if(empty($form_errors)){
				// Chech if the username & email we inserted is already exists.
				$checkUser = checkItem("Username", "users", $username);
				$checkEmail = checkItem("Email", "users", $email);

				if($checkUser == 1 || $checkEmail == 1){
					if($checkUser == 1 && $checkEmail == 1){
						$form_errors[] = "Sorry this username '" . $username . "' and email '" . $email ."' are already exist";
					}elseif($checkUser == 1){
						$form_errors[] = "Sorry this username '" . $username . "' is already exists";
					}elseif($checkEmail == 1){
						$form_errors[] = "Sorry this email '" . $email ."' is already exists";
					}
				}else{
					// Insert data preparation code
					$stm = $conn->prepare("INSERT INTO users(Username, Password, Email, Insert_Date, RegStatus)
											VALUES(:username, :hashedpass, :email, now(), 0) ");
					// Executing the insert preparation code by the data given from 'POST' request from the "signup form"
					$stm->execute(array(
						'username' 		=> $username,
						'hashedpass' 	=> sha1($pass),
						'email'			=> $email,
					));

					$sucMsg = "Succesful registeration";
				}
			}
		}
	}
?>
<div class="container login-signup">
	<h3 class="text-center">
		<span class="active" data-class="login">Login</span> | <span  data-class="signup">Signup</span>
	</h3>
	<!-- Login Form -->
	<form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input class="form-control" type="text" name="username" placeholder="Username" autocomplete="off"/>
		<input class="form-control password" type="password" name="pass" placeholder="Password" autocomplete="new-password"/>
		<i class="show-pass fa fa-eye fa-1x" id="login-eye"></i>
		<input class="btn btn-primary btn-block" name="submit_login" type="submit" value="Login"/>
	</form>

	<!-- Signup Form -->
	<form class="signup" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<input pattern=".{4,}" title="username must be more than 3 chars" class="form-control" type="text" name="username" placeholder="Username" autocomplete="off" required/>
		<input class="form-control" type="email" name="email" placeholder="Email" autocomplete="off" required/>
		<input minlength="3" class="form-control password" type="password" name="pass" placeholder="Password" autocomplete="new-password"/>
		<i class="show-pass fa fa-eye fa-1x" id="login-eye"></i>
		<input minlength="3" class="form-control password pass2" type="password" name="pass2" placeholder="Re-enter your password" autocomplete="new-password"/>
		<i class="show-pass fa fa-eye fa-1x" id="login-eye"></i>
		<input class="btn btn-primary btn-block" name="submit_signup" type="submit" value="Signup"/>
	</form>
	<div class="form_errors text-center">
		<?php 
			if(!empty($form_errors)){
				foreach($form_errors as $form_error){
					echo '<div class="alert alert-danger">' . $form_error . '</div>';
				}
			}elseif(isset($sucMsg)){echo $sucMsg;}
		?>
	</div>
</div>
<?php 
	include $temp."footer.php";
	ob_end_flush(); // Send the output buffer and turn off output buffering.
?>