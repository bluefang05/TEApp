// Variables
let questions;
let timer = document.getElementById("timer");
let seconds = 60;
let secondsRef = seconds;
let roundNumber = 0;
let answerAmount = 4;
let score = 0;
let isMuted = false;
let maxScore = localStorage.getItem("maxScore")
  ? parseInt(localStorage.getItem("maxScore"))
  : 0;
let lastQuestionIndex = -1;

fetch(
  "https://aspierd.com/otros/widgets/api/selecciona-1/api/gameQuestions.php?count=50"
)
  .then((response) => response.json())
  .then((data) => {
    console.log(data); // El array con las imágenes y emociones
    questions = data;
  });

document.getElementById("muteButton").addEventListener("click", toggleMute);
document
  .getElementById("resetMaxScoreButton")
  .addEventListener("click", resetMaxScore);
document.getElementById("resetButton").addEventListener("click", function () {
  location.reload();
});

// Game setup
let modal = Swal.fire({
  title: "Selecciona la emoción correcta",
  icon: "info",
  showCancelButton: false,
  confirmButtonText: "OK",
  confirmButtonColor: "#3B6978",
});

modal.then((result) => {
  setTimeout(() => {
    gamestart();
    playBackgroundMusic();
  }, 100);
});

// Game logic functions
function gamestart() {
  setRound();
  timer.innerHTML = seconds;
  let interval = setInterval(function () {
    timer.innerHTML = seconds;
    seconds -= 1;

    if (seconds < 0) {
      clearInterval(interval);
      finished.play();
      bg.pause();
      finishGame();
    }

    if (seconds < secondsRef * 0.2) {
      bg.playbackRate = 2;
    }
  }, 1000);
}

function setRound() {
  roundNumber = getRandomIndex(questions.length);
  usedQuestions = roundNumber;
  document.getElementById("answerBox").innerHTML = "";
  let selection = [];
  let questionsRef = [];

  for (let i = 0; i < questions.length; i++) {
    questionsRef.push(questions[i][1]);
  }

  questionsRef = Array.from(new Set(questionsRef));
  let correctAnswer = questions[roundNumber][1];
  selection.push("<div class='answer good'>" + correctAnswer);
  questionsRef = questionsRef.filter((item) => item !== correctAnswer);
  shuffle(questionsRef);

  for (let i = 1; i < answerAmount; i++) {
    selection.push("<div class='answer bad'>" + questionsRef[0]);
    questionsRef.splice(0, 1);
  }

  document.getElementById("questionBox").innerHTML =
    "<img id='portrait' src='http://aspierd.com/otros/widgets/api/selecciona-1/crudImages/" +
    questions[roundNumber][0] +
    "'>";
  shuffle(selection);
  selection.forEach((element) => {
    document.getElementById("answerBox").innerHTML += element;
  });

  makeButtonsClickable("answer");
}

function handleAnswerClick(isCorrect) {
  if (isCorrect) {
    score++;
    document.getElementById("score").innerHTML = score;
    right.play();
    setRound();
  } else {
    wrong.play();
    document.getElementById("timer-container").classList.add("shake");
    document.getElementById("timer-container").classList.add("red");
    setTimeout(function () {
      document.getElementById("timer-container").classList.remove("shake");
      document.getElementById("timer-container").classList.remove("red");
    }, 500);
  }
}

function finishGame() {
  let isNewRecord = false;
  if (score > maxScore) {
    maxScore = score;
    localStorage.setItem("maxScore", maxScore);
    isNewRecord = true;
  }

  Swal.fire({
    title: `Tu puntuación es ${score}`,
    text: isNewRecord
      ? `¡Nuevo récord! Máxima puntuación: ${maxScore}`
      : `Máxima puntuación: ${maxScore}`,
    icon: isNewRecord ? "success" : "info",
    confirmButtonText: "Reiniciar",
    confirmButtonColor: "#3B6978",
  }).then(() => {
    location.reload();
  });
}

// Utility functions
function shuffle(array) {
  array.sort(() => Math.random() - 0.5);
}

function getRandomIndex(n) {
  let newIndex = Math.floor(Math.random() * n);

  // Check if the index is the same as the last question index
  while (newIndex === lastQuestionIndex) {
    newIndex = Math.floor(Math.random() * n);
  }

  // Set the lastQuestionIndex to the newIndex
  lastQuestionIndex = newIndex;

  return newIndex;
}

function makeButtonsClickable(className) {
  var buttons = document.getElementsByClassName(className);
  for (let i = 0; i < buttons.length; i++) {
    buttons[i].onclick = function () {
      handleAnswerClick(this.classList.contains("good"));
    };
  }
}

function toggleMute() {
  isMuted = !isMuted;
  if (isMuted) {
    muteSounds();
    document.getElementById("muteButton").innerHTML = "Seguir música";
  } else {
    unmuteSounds();
    document.getElementById("muteButton").innerHTML = "Parar música";
  }
}

function muteSounds() {
  bg.muted = true;
  right.muted = true;
  wrong.muted = true;
  finished.muted = true;
}

function unmuteSounds() {
  bg.muted = false;
  right.muted = false;
  wrong.muted = false;
  finished.muted = false;
}

function resetMaxScore() {
  maxScore = 0;
  localStorage.removeItem("maxScore");
}
