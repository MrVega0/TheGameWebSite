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
  
    // Iterar a través de los números y crear las cartas
    for (let i = 0; i < uniqueNumbers.length; i++) {
      // Crear un nuevo elemento de carta
      const carta = document.createElement("div");
      carta.classList.add("card", "carta-generada");
  
      carta.style.backgroundImage = `url('./images/cartaBase.png')`;
      // Crear un elemento de número para mostrar el número aleatorio
      const numero = document.createElement("span");
      numero.classList.add("number-text");
      numero.textContent = uniqueNumbers[i];
  
      // Agregar el número a la carta
      carta.appendChild(numero);
  
      // Agregar la carta al contenedor
      cartasContainer.appendChild(carta);
    }
  }
  
  // Agregar un evento click a la imagen "deck" para generar los números aleatorios
  const deckImage = document.querySelector(".deck img");
  deckImage.addEventListener("click", agregarNumerosAleatorios);
  