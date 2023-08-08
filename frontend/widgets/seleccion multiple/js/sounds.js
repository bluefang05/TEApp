var right = new Audio("./assets/sounds/good.wav");
var wrong = new Audio("./assets/sounds/wrong.mp3");
wrong.volume = 1;
var finished = new Audio("./assets/sounds/finished.wav");
var bg = new Audio("./assets/sounds/bg.mp3");
bg.volume = 0.3;
bg.loop = true; // Add this to loop the background music
function playBackgroundMusic() {

    bg.play();
  }