function shuffle(array) {
    array.sort(() => Math.random() - 0.5);
  }

  function getRandomIndex(n) {
    return Math.floor(Math.random() * n);
  }