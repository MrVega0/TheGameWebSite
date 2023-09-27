let numCartasEnMano = 0;
let uniqueNumbers = [];
let draggedNumbers = [];
let manoCards = [];
let numCartasATirar = 0;
let cartasTiradas = [];

function generateUniqueRandomNumbers(count, excludeNumbers) {
  const numbers = Array.from({ length: 98 }, (_, index) => index + 2);
  const availableNumbers = numbers.filter(num => !excludeNumbers.includes(num));

  function shuffleArray(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
  }

  const shuffledNumbers = shuffleArray(availableNumbers);
  const result = shuffledNumbers.slice(0, count);
  return result;
}

function agregarNumerosAleatorios(cantidad) {
  const numerosTablero = document.querySelectorAll(".card .carta-numero");

  const nuevasCartas = generateUniqueRandomNumbers(cantidad, draggedNumbers);

  // Asignar números a las cartas en el tablero
  let index = 0;
  numerosTablero.forEach(numero => {
    if (!numero.textContent.trim()) {
      numero.textContent = nuevasCartas[index];
      index++;
    }
  });

  habilitarArrastreCartasTablero();
}
function habilitarArrastreCartasTablero() {
  const cartasTablero = document.querySelectorAll(".card");

  cartasTablero.forEach(carta => {
    carta.setAttribute("draggable", "true");
    carta.addEventListener("dragstart", dragstart_handler);
    carta.addEventListener("dragover", dragover_handler);
    carta.addEventListener("drop", drop_handler);
  });
}
function agregarNumerosAleatorios(cantidad) {
  const cartasContainer = document.getElementById("cartas-container");
  const numerosTablero = document.querySelectorAll(".card .carta-numero");

  const nuevasCartas = generateUniqueRandomNumbers(cantidad, draggedNumbers);

  // Encontrar el contenedor de la mano
  const manoContainer = document.querySelector(".cartas-container");

  // Verificar si encontramos el contenedor de la mano
  if (!manoContainer) {
    console.error("No se pudo encontrar el contenedor de la mano.");
    return;
  }

  nuevasCartas.forEach((numero, index) => {
    if (numCartasEnMano < 8) {
      const carta = document.createElement("div");
      carta.classList.add("card", "carta-mano");
      carta.style.backgroundImage = `url('./images/cartaBase.png')`;
      const numeroCarta = document.createElement("span");
      numeroCarta.classList.add("number-text");
      numeroCarta.textContent = numero;
      carta.appendChild(numeroCarta);
      carta.setAttribute("draggable", "true");
      carta.addEventListener("dragstart", e => {
        e.dataTransfer.setData("text/plain", carta.querySelector(".number-text").textContent);
        e.dataTransfer.setData("index", index.toString());
      });
      manoContainer.appendChild(carta);
      numCartasEnMano++;
      manoCards.push({ carta, numero, index });  // Registrar la carta
    }
  });

  // Asignar números a las cartas en el tablero
  numerosTablero.forEach((numero, index) => {
    if (!numerosTablero[index].textContent.trim()) {
      numerosTablero[index].textContent = nuevasCartas[index];
    }
  });

  habilitarArrastreCartasMano();
}

function habilitarArrastreCartasMano() {
  const cartasMano = document.querySelectorAll(".carta-mano");

  cartasMano.forEach(carta => {
    carta.setAttribute("draggable", "true");
    carta.addEventListener("dragstart", e => {
      e.dataTransfer.setData("text/plain", carta.querySelector(".number-text").textContent);
    });
  });
}

function tirarCartas(numCartas) {
  const numerosTablero = document.querySelectorAll(".card .carta-numero");

  const nuevasCartasEnTablero = [];

  // Tomar las cartas que se están tirando y eliminarlas del tablero y la mano
  for (let i = 0; i < numCartas; i++) {
    if (manoCards.length > i) {
      const cartaTirada = manoCards[i];
      const index = cartaTirada.index;

      // Restaura la carta en el tablero
      numerosTablero[index].textContent = cartaTirada.numero;
      nuevasCartasEnTablero.push(cartaTirada.numero);
    }
  }

  // Agregar nuevas cartas en la mano
  const nuevasCartas = generateUniqueRandomNumbers(numCartas, draggedNumbers);

  for (let i = 0; i < nuevasCartas.length; i++) {
    if (numCartasEnMano < 8) {
      const carta = manoCards[i].carta;
      const numero = nuevasCartas[i];
      manoCards[i].numero = numero;
      const numeroCarta = carta.querySelector(".number-text");
      numeroCarta.textContent = numero;
      numCartasEnMano++;
    }
  }

  // Asignar números a las cartas en el tablero
  numerosTablero.forEach((numero, index) => {
    if (nuevasCartasEnTablero[index]) {
      numero.textContent = nuevasCartasEnTablero[index]; // Actualiza el número en el tablero
    }
  });

  // Habilitar el arrastre de cartas en la mano
  habilitarArrastreCartasMano();
}

function dragover_handler(e) {
  e.preventDefault();
}

function dragstart_handler(e) {
  const cartaArrastrada = manoCards.find(card => card.carta === e.target);
  const index = cartaArrastrada.index;
  e.dataTransfer.setData("index", index.toString());
}

function drop_handler(e) {
  e.preventDefault();
  const index = e.dataTransfer.getData("index");
  const cartaArrastrada = manoCards.find(card => card.index === parseInt(index));

  if (cartaArrastrada) {
    const numeroTablero = e.target.querySelector('.carta-numero');
    if (numeroTablero && !numeroTablero.textContent.trim()) {
      numeroTablero.textContent = cartaArrastrada.numero;
    }
  }
}

// Permitir soltar cartas en las casillas del tablero
const cartasTablero = document.querySelectorAll(".card");
cartasTablero.forEach(carta => {
  carta.addEventListener("dragover", dragover_handler);
  carta.addEventListener("drop", drop_handler);
});

const deckImage = document.querySelector(".deck img");
deckImage.addEventListener("click", () => {
  if (numCartasATirar > 0) {
    tirarCartas(numCartasATirar);
  } else {
    // Solo agregar las cartas que falten para llegar a 8
    agregarNumerosAleatorios(8 - numCartasEnMano);
  }
  numCartasATirar = 0;
});
