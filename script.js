// Función para generar números aleatorios únicos entre 2 y 99
function generateUniqueRandomNumbers(count, min, max) {
  const numbers = [];
  while (numbers.length < count) {
    const random = Math.floor(Math.random() * (max - min + 1)) + min;
    if (!numbers.includes(random)) {
      numbers.push(random);
    }
  }
  return numbers;
}

// Función para agregar los números aleatorios al contenedor de cartas
function agregarNumerosAleatorios() {
  // Obtener el contenedor de las cartas
  const cartasContainer = document.getElementById("cartas-container");

  // Generar 8 números aleatorios únicos entre 2 y 99
  const uniqueNumbers = generateUniqueRandomNumbers(8, 2, 99);

  // Limpiar el contenido anterior del contenedor
  cartasContainer.innerHTML = "";

  // Crear un arreglo para almacenar las cartas generadas
  const cartasGeneradas = [];

  // Iterar a través de los números y crear las cartas
  uniqueNumbers.forEach(numero => {
    // Crear un nuevo elemento de carta
    const carta = document.createElement("div");
    carta.classList.add("card", "carta-generada");
    carta.style.backgroundImage = `url('./images/cartaBase.png')`;

    // Crear un elemento de número para mostrar el número aleatorio
    const numeroCarta = document.createElement("span");
    numeroCarta.classList.add("number-text");
    numeroCarta.textContent = numero;

    // Agregar el número a la carta
    carta.appendChild(numeroCarta);

    // Agregar la carta al contenedor
    cartasContainer.appendChild(carta);

    // Hacer que las nuevas cartas generadas sean arrastrables
    carta.setAttribute("draggable", "true");
    carta.addEventListener("dragstart", e => {
      e.dataTransfer.setData("text/plain", numero.toString());
      cartasGeneradas.push(carta); // Agregar la carta al arreglo
    });
  });

  // Obtener los elementos de números en el tablero
  const numerosTablero = document.querySelectorAll(".card div.carta-numero");

  // Agregar un evento 'dragover' para permitir soltar en los números
  numerosTablero.forEach(numero => {
    numero.addEventListener("dragover", e => {
      e.preventDefault();
    });

    // Agregar un evento 'drop' para cambiar el número y eliminar la carta de la mano
    numero.addEventListener("drop", e => {
      e.preventDefault();
      const draggedNumber = e.dataTransfer.getData("text/plain");

      // Buscar la carta arrastrada en el arreglo y eliminarla del DOM y del arreglo
      const cartaArrastrada = cartasGeneradas.find(carta => carta.textContent === draggedNumber);
      if (cartaArrastrada) {
        cartaArrastrada.remove();
        cartasGeneradas.splice(cartasGeneradas.indexOf(cartaArrastrada), 1);
      }

      numero.textContent = draggedNumber;
    });
  });
}

// Agregar un evento click a la imagen "deck" para generar los números aleatorios
const deckImage = document.querySelector(".deck img");
deckImage.addEventListener("click", agregarNumerosAleatorios);
