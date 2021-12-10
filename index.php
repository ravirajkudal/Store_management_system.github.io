<?php

	include('db.php');

	$authentication_error = "";
	$registration_msg_flag = 0;

	if($_POST){
		$email = $_POST['email'];
		$password = $_POST['password'];

		if($_POST['action_type'] == "Sign in"){
			$sql = "SELECT * FROM registration WHERE email = '$email' AND password= '$password'";
			$result = mysqli_query($db,$sql);
			if(!$result->num_rows){
				$authentication_error = "Invalid Email or Password";
			} else {
				if($row=mysqli_fetch_assoc($result)){
					session_start();
					$_SESSION['user_id'] = 	$row['id'];
					$_SESSION['user_name'] = 	$row['user_name'];
					$_SESSION['email'] = $row['email'];
					header("location:home_page.php");
				} else{

				}
			}
		} else if($_POST['action_type'] == "Sign up"){
		
			$sql = "SELECT * FROM registration WHERE email = '$email'";
			$result = mysqli_query($db,$sql);
			if($result->num_rows){
				$registration_msg_flag = 1;
			} else {
				$user_name = $_POST['username'];
				$sql = "INSERT INTO registration (user_name, email,password) VALUES ('$user_name','$email','$password')";

				$result = mysqli_query($db,$sql);
				$registration_msg_flag = 2;
			}
			
		}
	}
?>


<html>
	<head>
		<link rel="stylesheet" href="css/style.css">
		<script src="js/jquery.min.js"></script>
	</head>

	<body>
		<div class="container" id="container">
			<div class="form-container sign-up-container">
				<form action="index.php" method="POST" id="frmSingUp">
					<h4 id="registrationMsg" class="success-msg"></h4>
					<h1>Create Account</h1>
					<input type="hidden" name="action_type" value="Sign up">
					<input type="text" placeholder="Username" name="username" id="txtName" onkeypress="return isText(event);" required/>
					<span class="danger" id="txtNameError"></span>
					<input type="email" placeholder="Email" name="email" id="txtEmail" required/>
					<span class="danger" id="txtEmailError"></span>
					<input type="password" placeholder="Password" name="password" id="txtPassword" required />
					<span class="danger" id="txtPasswordError"></span>
					<input type="password" placeholder="Confirm Password" name="confirm_password" id="txtConfirmPassword" required />
					<span class="danger" id="txtConfirmPasswordError"></span>
					<button type="button" name="sign_up" id="btnSignUp">Sign Up</button><br>
				</form>
			</div>
			<div class="form-container sign-in-container">
				<form action="index.php" method="POST" id="frmSignIn">
					<h1>Sign in</h1>
					<input type="hidden" name="action_type" value="Sign in">
					<input type="text" placeholder="Email" required name="email" id="txtLoginEmail" />
					<span class="danger" id="txtLoginEmailError"></span>
					<input type="password" placeholder="Password" required name="password" id="txtLoginPassword" />
					<span class="danger" id="txtLoginPasswordError"></span>
					<br>
					<button type="button" name="sign_in" id="btnSignIn">Sign In</button><br>
				</form>
			</div>
			<div class="overlay-container">
				<div class="overlay">
					<div class="overlay-panel overlay-left">
						<p>Keep connected with us please login with your info</p>
						<button class="ghost" id="signIn">Sign In</button>
					</div>
					<div class="overlay-panel overlay-right">
						<h1>Start with us and make the business easy</h1><br>
						<button class="ghost" id="signUp">Sign Up</button>
					</div>
				</div>
			</div>
		</div>

		<script>

			const signUpButton = document.getElementById('signUp');
			const signInButton = document.getElementById('signIn');
			const container = document.getElementById('container');

			signUpButton.addEventListener('click', () => {
				container.classList.add("right-panel-active");
			});

			signInButton.addEventListener('click', () => {
				container.classList.remove("right-panel-active");
			});

			$(document).ready(function(){
				var authenticationError = "<?php echo $authentication_error;?>";
				var registrationMsgFlag = "<?php echo $registration_msg_flag;?>";
				if(registrationMsgFlag==1){
					container.classList.add("right-panel-active");
					$("#registrationMsg").attr("class","danger");
					$("#registrationMsg").html("User already registred");
					resetMsg();
				} else if(registrationMsgFlag==2){
					container.classList.add("right-panel-active");
					$("#registrationMsg").attr("class","success-msg");
					$("#registrationMsg").html("Registration successfully!");
					resetMsg();
				}

				$("#txtLoginPasswordError").html(authenticationError);
				
				$("#btnSignUp").click(function(){
					errorClear();
					valCheck();
				});

				$("#btnSignIn").click(function(){
					errorClear();
					valLoginCheck();
				});
			});

			function valCheck(){
				//var v = false;
				if($('#txtName').val().trim()==""){
					$('#txtName').focus();
					$("#txtNameError").html("Please enter Username");
				} else if($('#txtEmail').val().trim()=="" || !validateEmail('txtEmail')){
					$('#txtEmail').focus();
					$("#txtEmailError").html("Please enter valid Email");
				} else if($('#txtPassword').val().trim().length<8){
					$('#txtPassword').focus();
					$("#txtPasswordError").html("Please enter at least 8 characters Password");
				} else if($('#txtConfirmPassword').val().trim().length<8){
					$('#txtConfirmPassword').focus();
					$("#txtConfirmPasswordError").html("Please enter at least 8 characters Confirm password");
				} else if($('#txtPassword').val() != $('#txtConfirmPassword').val()){
					$('#txtConfirmPassword').focus();
					$("#txtConfirmPasswordError").html("Password and Confirm Password does not match");
				} else{
					$("#frmSingUp").submit();
				}

				//return v;
			}

			function valLoginCheck(){
				//var v = false;
				if($('#txtLoginEmail').val().trim()=="" || !validateEmail('txtLoginEmail')){
					$('#txtLoginEmail').focus();
					$("#txtLoginEmailError").html("Please enter valid Email");
				} else if($('#txtLoginPassword').val().trim().length<8){
					$('#txtLoginPassword').focus();
					$("#txtLoginPasswordError").html("Please enter at least 8 characters Password");
				} else{
					$("#frmSignIn").submit();
				}
				//return v;
			}

			function errorClear(){
				$("#txtNameError").html("");
				$("#txtEmailError").html("");
				$("#txtPasswordError").html("");
				$("#txtConfirmPasswordError").html("");
				$("#txtLoginEmailError").html("");
				$("#txtLoginPasswordError").html("");

			}

			function resetMsg(){
				setTimeout(function(){
					$("#registrationMsg").html("");
				},4000)
			}
			function validateEmail(id){       
				var email = document.getElementById(id).value;                        
				var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
				if (reg.test(email)){
					return true; 
				}
				else{        			   
					return false;
				}
			}

			function isText(evt){
				var iKeyCode = (evt.which) ? evt.which : evt.KeyCode
				if((iKeyCode >= 65 && iKeyCode <=90) || (iKeyCode >= 97 && iKeyCode <= 122) || iKeyCode == 8 || iKeyCode == 32 || iKeyCode ==9){
					return true;
				}
				return false;
			}
		</script>

	</body>
</html>