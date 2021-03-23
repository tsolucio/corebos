<!--added by nedi with love-->
<link rel="stylesheet" href="themes/login/lds/sfdc_210.css">
<style>

a {
	color: #0070d2;
}

body {
  
	background-color: #F4F6F9 ;
}

#content, .container {
	
	background-color: rgba(255,255,255,0.30);
	padding-top: 20px;
	
}

#header {
	color: #16325c;
}

body {
	display: table;
	width: 100%;
	background: linear-gradient(180deg,rgba(4,87,233,0.66) 0%, #f4f6f9 85%);
	
};
}

#content {
	margin-bottom: 20px;
}

#wrap {
	height: 100%;
}

#right {
	vertical-align: middle;
}

.errorMessage {
	font-size: 12px;
	color: #982121;
}

#left {
	position: absolute;
	top: 50%;
	left: 50%; 
	transform: translate(-50%, -50%);
	width: 100%;
}

/*added by nedi codepen*/
canvas {
  background: linear-gradient(#0070d2, #9198e5);
}


</style>
</head>

<body onload="set_focus()" data-gr-c-s-loaded="true"> 
<canvas id="canvas"></canvas>


	<div id="left" class="pr">
		<div id="wrap">
			<div id="main">
				<div id="wrapper">
					<div id="logo_wrapper" class="standard_logo_wrapper mb24">
						<h1 style="height: 100%; display: table-cell; vertical-align: bottom;">
							<img id="logo" class="standard_logo"
								src="{$COMPANY_DETAILS.companylogo}"
								alt="{$coreBOS_uiapp_name}" border="0" name="logo">
						</h1>
					</div>
					<h2 id="header" class="mb12" style="display: none;"></h2>
					<div id="content" style="display: block;">
						<div id="chooser" style="display: none">
							<div class="loginError" id="chooser_error" style="display: block;"></div>
						</div>
						<div id="theloginform" style="display: block;">
							{if $LOGIN_ERROR neq ''}
							<div class="errorMessage">{$LOGIN_ERROR}</div>
							{/if}
							<form method="post" id="login_form" action="index.php" target="_top" autocomplete="off" novalidate="novalidate">
								<input type="hidden" name="module" value="Users" />
								<input type="hidden" name="action" value="Authenticate" />
								<input type="hidden" name="return_module" value="Users" />
								<input type="hidden" name="return_action" value="Login" />
								<div id="usernamegroup" class="inputgroup"> 
								
									<div id="username_container">
										<input class="input r4 wide mb16 mt8 username" type="email" value="" name="user_name" id="username" style="display: block;" placeholder="Username">
									</div>
								</div>
								
								<input class="input r4 wide mb16 mt8 password" type="password" id="password" name="user_password"
									onkeypress="checkCaps(event)" autocomplete="off" placeholder="Password">
								<div id="pwcaps" class="mb16" style="display: none">
									<img id="pwcapsicon" alt="{'CapsLockActive'|getTranslatedString}" width="12" src="themes/login/lds/capslock_blue.png">
									{'CapsLockActive'|getTranslatedString}
								</div>
								<input class="button r4 wide primary" type="submit" id="Login" name="Login" value="{'StartSession'|getTranslatedString}">
							</form>
						</div>
					</div>
				</div>
			</div>

			<div id="footer" style="color:white">
				© Made by   {$coreBOS_uiapp_name}.
			</div>
		</div>

	</div>
<script>
var canvas = document.getElementById("canvas"),
    ctx = canvas.getContext('2d');

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

var stars = [], // Array that contains the stars
    FPS = 60, // Frames per second
    x = 100, // Number of stars
    mouse = {
      x: 0,
      y: 0
    };  // mouse location

// Push stars to array

for (var i = 0; i < x; i++) {
  stars.push({
    x: Math.random() * canvas.width,
    y: Math.random() * canvas.height,
    radius: Math.random() * 1 + 1,
    vx: Math.floor(Math.random() * 50) - 25,
    vy: Math.floor(Math.random() * 50) - 25
  });
}

// Draw the scene

function draw() {
  ctx.clearRect(0,0,canvas.width,canvas.height);
  
  ctx.globalCompositeOperation = "lighter";
  
  for (var i = 0, x = stars.length; i < x; i++) {
    var s = stars[i];
  
    ctx.fillStyle = "#fff";
    ctx.beginPath();
    ctx.arc(s.x, s.y, s.radius, 0, 2 * Math.PI);
    ctx.fill();
    ctx.fillStyle = 'black';
    ctx.stroke();
  }
  
  ctx.beginPath();
  for (var i = 0, x = stars.length; i < x; i++) {
    var starI = stars[i];
    ctx.moveTo(starI.x,starI.y); 
    if(distance(mouse, starI) < 150) ctx.lineTo(mouse.x, mouse.y);
    for (var j = 0, x = stars.length; j < x; j++) {
      var starII = stars[j];
      if(distance(starI, starII) < 150) {
        //ctx.globalAlpha = (1 / 150 * distance(starI, starII).toFixed(1));
        ctx.lineTo(starII.x,starII.y); 
      }
    }
  }
  ctx.lineWidth = 0.05;
  ctx.strokeStyle = 'white';
  ctx.stroke();
}

function distance( point1, point2 ){
  var xs = 0;
  var ys = 0;
 
  xs = point2.x - point1.x;
  xs = xs * xs;
 
  ys = point2.y - point1.y;
  ys = ys * ys;
 
  return Math.sqrt( xs + ys );
}

// Update star locations

function update() {
  for (var i = 0, x = stars.length; i < x; i++) {
    var s = stars[i];
  
    s.x += s.vx / FPS;
    s.y += s.vy / FPS;
    
    if (s.x < 0 || s.x > canvas.width) s.vx = -s.vx;
    if (s.y < 0 || s.y > canvas.height) s.vy = -s.vy;
  }
}

canvas.addEventListener('mousemove', function(e){
  mouse.x = e.clientX;
  mouse.y = e.clientY;
});

// Update and draw

function tick() {
  draw();
  update();
  requestAnimationFrame(tick);
}

tick();
</script>
</body>