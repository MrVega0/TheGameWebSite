<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="style/thegame.css">
    <title>The Game</title>

</head>
<?php
$gameState = isset($_COOKIE['gameState']) ? json_decode($_COOKIE['gameState'], true) : null;

// Si hay un estado de juego almacenado, usarlo para restaurar el juego
if ($gameState) {
    $mazo = $gameState['mazo'];
    $cartasColocadasTotales = $gameState['cartasColocadasTotales'];
    $cartasColocadas = $gameState['cartasColocadas'];

    $estadoTablero = $gameState['estadoTablero'];
    $estadoMano = $gameState['estadoMano'];
}

$mazo = generarMazo();
// Reorganiza aleatoriamente el mazo
shuffle($mazo);

// Representa una carta
class Card
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }
}

// Genera el mazo con números del 2 al 99
function generarMazo()
{
    $mazo = [];
    for ($i = 2; $i <= 99; $i++) {
        $mazo[] = new Card($i);
    }
    return $mazo;
}


// Tomar las primeras 8 cartas del mazo para la mano
$cartas_sacadas = array_slice($mazo, 0, 8);
?>

<body>

    <div class="board">
        <?php
        // Mostrar las primeras cartas en el tablero (dos cartas con el número 1 y dos cartas con el número 100)
        $primeras_cartas = [
            [1],
            [1],
            [100],
            [100]
        ];

        foreach ($primeras_cartas as $index => $card) {

            echo "<div class='pile' id='pile_$index' ondrop='drop(event, $index)' ondragover='allowDrop(event)'>";
            echo "{$card[0]}";  // Mostrar el número de la carta
        ?>

        <?php
            echo "</div>";
        }
        ?>


    </div>

    <div class="hand" id="hand">
        <?php
        foreach ($cartas_sacadas as $index => $card) {

            echo "<div class='card' id='card_$index' draggable='true' ondragstart='drag(event)'>{$card->getNumber()}</div>";
        }
        ?>
    </div>

    <div>
        <button id='exitButton'> Salir</button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        /* ----------------------------SweetAlert2------------------------------------------- */

        function mostrarAlert() {
            Swal.fire({
                title: '¿Quieres salir?',
                text: "No te preocupes, luego puedes continuar tu partida actual.",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Salir.'
            }).then((result) => {
                if (result.isConfirmed) {

                    window.history.back();
                }
            });
        }

        const exitButton = document.getElementById('exitButton');
        exitButton.addEventListener('click', mostrarAlert);
    </script>


    <script>
        restaurarEstadoJuego();
        /* ----------------------------Funciones------------------------------------------- */

        var intentosColocacion = 0;
        var mazo = <?php echo json_encode(array_map(function ($card) {
                        return array('number' => $card->getNumber());
                    }, $mazo)); ?>;
        console.log('Mazo en JavaScript:', mazo);
        var draggedCardId;
        var hand = [];
        var cartasColocadasTotales = <?php echo isset($gameState['cartasColocadasTotales']) ? $gameState['cartasColocadasTotales'] : 0; ?>;//Para checar si gané o no nomas cambie este a 97 y tire una carta :)
        var cartasColocadas = <?php echo isset($gameState['cartasColocadas']) ? $gameState['cartasColocadas'] : 0; ?>;

        var button = document.createElement('button');
        //button.innerText = 'Robar/Jalar';
        button.id = 'robarButton';
        button.addEventListener('click', function() {
            mostrarCartasRestantesEnMazo();

            if (cartasColocadas >= 2) {
                drawCards(cartasColocadas);
                cartasColocadas = 0;
            } else {
                Swal.fire('Para jalar debes colocar por lo menos 2 cartas.');
            }
        });
        // Añade el botón al cuerpo del documento
        document.body.appendChild(button);

        // Reglas de colocación en las pilas
        var rules = {
            0: function(cardNumber) {
                return cardNumber > parseInt(document.getElementById('pile_0').innerText);
            },
            1: function(cardNumber) {
                return cardNumber > parseInt(document.getElementById('pile_1').innerText);
            },
            2: function(cardNumber) {
                return cardNumber < parseInt(document.getElementById('pile_2').innerText);
            },
            3: function(cardNumber) {
                return cardNumber < parseInt(document.getElementById('pile_3').innerText);
            }
        };

        function verificarPerdida() {
            var colocacionPosible = false;
            var handCards = document.getElementById('hand').getElementsByClassName('card');

            for (var i = 0; i < handCards.length; i++) {
                var cardNumber = parseInt(handCards[i].innerText);

                for (var j = 0; j < 4; j++) {
                    if (rules[j](cardNumber) || checkException(cardNumber, parseInt(document.getElementById(`pile_${j}`).innerText))) {
                        colocacionPosible = true;
                        break;
                    }
                }

                if (colocacionPosible) {
                    break;
                }
            }

            var robarPosible = cartasColocadas >= 2;

            console.log('Colocación posible:', colocacionPosible);
            console.log('Robar posible:', robarPosible);

            if (!colocacionPosible && !robarPosible) {
                Swal.fire({
                    title: '¡Vaya!',
                    text: 'Parece que perdiste.',
                    imageUrl: 'images/YouLose.gif',
                    //imageUrl: 'https://img1.picmix.com/output/stamp/normal/7/8/6/4/1984687_608ed.gif',
                    imageWidth: 400,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                })
            }
        }


        function ordenarCartasEnMano() {
            var manoDiv = document.getElementById('hand');
            var cards = manoDiv.getElementsByClassName('card');
            var sortedCards = Array.from(cards).sort(function(a, b) {
                return parseInt(a.innerText) - parseInt(b.innerText);
            });

            for (var i = 0; i < sortedCards.length; i++) {
                manoDiv.removeChild(sortedCards[i]);
                manoDiv.appendChild(sortedCards[i]);

            }

        }

        function drawCards(numCardsToDraw) {
            console.log('Intentando robar cartas...');
            for (var i = 0; i < numCardsToDraw; i++) {
                if (mazo.length > 0) {
                    var drawnCard = mazo.pop();
                    hand.push(drawnCard);
                    console.log('Carta robada:', drawnCard);

                    var cardElement = document.createElement('div');
                    cardElement.classList.add('card');
                    cardElement.draggable = true;
                    cardElement.id = 'hand_card_' + (hand.length - 1);
                    cardElement.innerText = drawnCard.number;
                    document.getElementById('hand').appendChild(cardElement);
                    cardElement.addEventListener('dragstart', drag);
                } else {
                    console.log('No quedan más cartas en el mazo.');
                }
            }
            verificarPerdida();
            ordenarCartasEnMano();

        }
        ordenarCartasEnMano();



        function removeCardFromHand(cardId) {
            var card = document.getElementById(cardId);
            if (card) {
                card.remove();
            }
        }

        function checkException(cardNumber, pileNumber) {
            return (cardNumber === pileNumber - 10) || (cardNumber === pileNumber + 10);
        }

        function alertPlaceCard(alertaCartasColocadas) {


        }

        function placeCard(pileIndex) {
            if (draggedCardId) {
                var draggedCard = document.getElementById(draggedCardId);
                var pile = document.getElementById(`pile_${pileIndex}`);

                if (pile) {
                    var cardNumber = parseInt(draggedCard.innerText);
                    var pileNumber = parseInt(pile.innerText);

                    if (rules[pileIndex](cardNumber) || checkException(cardNumber, pileNumber)) {
                        pile.innerHTML = draggedCard.innerHTML;
                        showCardsInPiles();
                        removeCardFromHand(draggedCardId);
                        removeCardFromMazo(cardNumber);
                        cartasColocadasTotales++;
                        cartasColocadas++;
                        console.log("cartas a regresar: ", cartasColocadas);
                        console.log("cartas totales colocadas: ", cartasColocadasTotales);

                        if (cartasColocadasTotales === 98) {
                            alert('¡Felicidades! Has colocado todas las cartas. ¡Ganaste!');
                        }

                    } else {
                        alert('No se puede colocar esta carta según las reglas.');
                        console.log("No se puede colocar esta carta según las reglas.");
                    }
                }

                draggedCardId = null;
            }
            guardarEstadoJuego();
            verificarPerdida();
            ordenarCartasEnMano();
        }

        function removeCardFromMazo(cardNumber) {
            mazo = mazo.filter(function(card) {
                return card.number !== cardNumber;
            });
        }

        function drop(event, pileIndex) {
            event.preventDefault();
            placeCard(pileIndex);
        }

        function updatePile(pileIndex, cardValue) {
            var pile = document.getElementById(`pile_${pileIndex}`);
            pile.innerHTML = cardValue;
        }

        function allowDrop(event) {
            event.preventDefault();
        }

        function drag(event) {
            draggedCardId = event.target.id;
            event.dataTransfer.setData('text/plain', event.target.id);
        }

        function showCardsInPiles() {
            for (var i = 0; i < 4; i++) {
                var pile = document.getElementById(`pile_${i}`);
                console.log(`Pila ${i + 1}: ${pile.innerText.trim()}`);
            }
        }

        //este script es para poner cosas en consola
        // Mostrar el mazo
        console.log("Mazo original:", <?php echo json_encode(array_map(function ($card) {
                                            return $card->getNumber();
                                        }, $mazo)); ?>);

        // Mostrar la mano
        console.log("Mano:", <?php echo json_encode(array_map(function ($card) {
                                    return $card->getNumber();
                                }, $cartas_sacadas)); ?>);

        function showCardsInPiles() {
            for (var i = 0; i < 4; i++) {
                var pile = document.getElementById(`pile_${i}`);
                console.log(`Pila ${i + 1}: ${pile.innerText.trim()}`);
            }
        }

        function mostrarCartasRestantesEnMazo() {
            var cartasRestantes = mazo.slice(hand.length + cartasColocadasTotales).map(function(card) {
                return card.number;
            });
            console.log('Cartas restantes en el mazo:', cartasRestantes);
        }

        function obtenerEstadoTablero() {
            var estadoTablero = [];

            for (var i = 0; i < 4; i++) {
                var pila = document.getElementById(`pile_${i}`);
                var cartasEnPila = [];

                // Itera sobre las cartas en la pila y obtén sus números
                for (var j = 0; j < pila.childNodes.length; j++) {
                    var numeroCarta = parseInt(pila.childNodes[j].textContent);
                    cartasEnPila.push(numeroCarta);
                }

                console.log(`Contenido de la pila ${i}:`, cartasEnPila); // Verificar contenido de la pila
                estadoTablero.push(cartasEnPila);
            }

            return estadoTablero;
        }


        function obtenerEstadoMano() {
            var estadoMano = [];

            var mano = document.getElementById('hand');

            for (var i = 0; i < mano.childNodes.length; i++) {
                var numeroCarta = parseInt(mano.childNodes[i].innerText);
                estadoMano.push(numeroCarta);
            }

            return estadoMano;
        }


        function guardarEstadoJuego() {
            console.log('Guardando estado del juego...');
            console.log('BPcartasColocadasTotales:', cartasColocadasTotales);
            console.log('BPcartasColocadas:', cartasColocadas);

            var gameState = {
                mazo: mazo,
                cartasColocadasTotales: cartasColocadasTotales,
                cartasColocadas: cartasColocadas,
                // Guardar el estado del tablero y la mano
                estadoTablero: obtenerEstadoTablero(),
                estadoMano: obtenerEstadoMano()
            };
            document.cookie = "gameState=" + JSON.stringify(gameState) + "; path=/";
        }


        window.addEventListener('beforeunload', function(event) {
            guardarEstadoJuego();
        });

        function getCookie(name) {
            var nameEQ = name + "=";
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i];
                while (cookie.charAt(0) === ' ') cookie = cookie.substring(1, cookie.length);
                if (cookie.indexOf(nameEQ) === 0) return cookie.substring(nameEQ.length, cookie.length);
            }
            return null;
        }


        function restaurarEstadoJuego() {
            var gameStateCookie = getCookie('gameState');
            if (gameStateCookie) {
                var gameState = JSON.parse(gameStateCookie);

                // Restaurar el estado del mazo
                mazo = gameState.mazo;

                // Restaurar el número total de cartas colocadas
                cartasColocadasTotales = gameState.cartasColocadasTotales;
                cartasColocadas = gameState.cartasColocadas;

                // Restaurar el estado del tablero
                var estadoTablero = gameState.estadoTablero;
                for (var i = 0; i < estadoTablero.length; i++) {
                    var pile = document.getElementById(`pile_${i}`);
                    pile.innerHTML = ''; // Limpiar la pila antes de restaurar
                    for (var j = 0; j < estadoTablero[i].length; j++) {
                        var cardNumber = estadoTablero[i][j];
                        var cardElement = document.createElement('div');
                        cardElement.classList.add('card');
                        cardElement.innerText = cardNumber;
                        pile.appendChild(cardElement);
                    }
                }

                // Restaurar el estado de la mano
                var estadoMano = gameState.estadoMano;
                var manoDiv = document.getElementById('hand');
                manoDiv.innerHTML = ''; // Limpiar la mano antes de restaurar
                for (var j = 0; j < estadoMano.length; j++) {
                    if (estadoMano[j] !== null) {
                        var cardElement = document.createElement('div');
                        cardElement.classList.add('card');
                        cardElement.draggable = true;
                        cardElement.id = `hand_card_${j}`;
                        cardElement.innerText = estadoMano[j];
                        manoDiv.appendChild(cardElement);
                        cardElement.addEventListener('dragstart', drag);
                    }
                }
            }
        }
    </script>

</body>

<script>
    /* 
   //################################### para pruebas unitaras ##########################


    function isMoveValid(cardNumber, pileNumber) {
    const rule = (cardNumber, pileNumber) => cardNumber < pileNumber;
    const exception = (cardNumber, pileNumber) => Math.abs(cardNumber - pileNumber) === 10;
    return rule(cardNumber, pileNumber) || exception(cardNumber, pileNumber);

}

// Función para probar isMoveValid
function runTests() {
    console.log('Testing isMoveValid function:');

    // Casos de prueba
    const testCases = [
        { card: 20, pile: 30, expected: true },  // Regla básica de pila 1
        { card: 20, pile: 30, expected: true }, // Excepción (10 menos)
        { card: 30, pile: 20, expected: true }, // Excepción (10 más)
    ];

    for (const { card, pile, expected } of testCases) {
        const result = isMoveValid(card, pile);
        console.log(`Card: ${card}, Pile: ${pile}, Expected: ${expected}, Result: ${result}`);
        console.log(`Test ${result === expected ? 'passed' : 'failed'}\n`);
    }
}

// Ejecutar las pruebas
runTests();

// Aquí colocas el código para simular la colocación de 98 cartas
for (let i = 0; i < 98; i++) {
        let randomPileIndex = Math.floor(Math.random() * 4);
        let randomCardNumber = Math.floor(Math.random() * 98) + 2;
        console.log(`Colocando carta ${randomCardNumber} en la pila ${randomPileIndex}`);
        placeCard(randomPileIndex);

        // Añade un mensaje para verificar que placeCard se está llamando
        console.log("Llamada a placeCard");
    }
    
*/
</script>

</html>