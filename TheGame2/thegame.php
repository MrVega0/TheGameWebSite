<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="style/thegame.css">
    <title>The Game</title>

</head>
<?php
/* al parecer esto no es necesario y no funcionaba
if (isset($gameState)) {
    $mazo = $gameState['mazo'];
    $cartasColocadasTotales = $gameState['cartasColocadasTotales'];
    $cartasColocadas = $gameState['cartasColocadas'];

    $estadoTablero = $gameState['estadoTablero'];
    $estadoMano = $gameState['estadoMano'];
    $semilla = $gameState['semilla'];

} else {
}  */

    $semilla = mt_rand();
    $mazo = generarMazoConSemilla($semilla);
    shuffle($mazo);

echo '<div id="divsemilla">Semilla: ' . $semilla . '</div>';


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

function generarMazoConSemilla($semilla)
{
    mt_srand($semilla);
    $mazo = [];

    for ($i = 2; $i <= 99; $i++) {
        $mazo[] = new Card($i);
    }

    shuffle($mazo);

    return $mazo;
}

/*
function generarMazo()
{
    $mazo = [];
    $usedCards = [];

    // Verificar si hay información en localStorage
    echo '<script>';
    echo 'var gameState = localStorage.getItem("gameState");';
    echo 'if (gameState) {';
    echo '    gameState = JSON.parse(gameState);';
    echo '    if (gameState.estadoMano) {';
    echo '        usedCards = gameState.estadoMano;';
    echo '    }';
    echo '}';
    echo '</script>';

    for ($i = 2; $i <= 99; $i++) {
        if (!in_array($i, $usedCards)) {
            $mazo[] = new Card($i);
        }
    }

    return $mazo;
}  */

?>

<body>

    <div class="board">
        <?php
        $primeras_cartas = [
            [1],
            [1],
            [100],
            [100]
        ];

        foreach ($primeras_cartas as $index => $card) {

            echo "<div class='pile' id='pile_$index' ondrop='drop(event, $index)' ondragover='allowDrop(event)'>";
            echo "{$card[0]}";
        ?>

        <?php
            echo "</div>";
        }
        ?>


    </div>

    <div class="hand" id="hand">
        <?php
        echo '<div id="startMessage" style="background-color:white;" >Haz click en el mazo para comenzar</div>';
        ?>


    </div>
    <div>
        <button id='exitButton'> Salir </button>
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
        var salirperdiste = false;
        var mazo = <?php echo json_encode(array_map(function ($card) {
                        return array('number' => $card->getNumber());
                    }, $mazo)); ?>;
        console.log('Mazo en JavaScript:', mazo);
        var draggedCardId;
        var hand = [];
        var cartasColocadasTotales = 0; //Para checar si gané o no nomas cambie este a 97 y tire una carta :)
        var cartasColocadas = 8;
        var gameState = JSON.parse(localStorage.getItem('gameState'));
        if (gameState) {
            cartasColocadasTotales = gameState.cartasColocadasTotales;
            cartasColocadas = gameState.cartasColocadas;
        }
        var button = document.createElement('button');
        //button.innerText = 'Robar/Jalar';
        button.id = 'robarButton';
        button.addEventListener('click', function() {
            mostrarCartasRestantesEnMazo();

            if (cartasColocadas >= 2) {
                drawCards(cartasColocadas);
                cartasColocadas = 0;
                verificarPerdida();
                var startMessage = document.getElementById('startMessage');
                if (startMessage) {
                    startMessage.style.display = 'none';
                }
            } else {
                Swal.fire('Para jalar debes colocar por lo menos 2 cartas.');

            }
        });

        document.body.appendChild(button);

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
                    imageWidth: 400,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Salir.'
                }).then((result) => {
                    if (result.isConfirmed) {
                        salirperdiste = true;
                        window.history.back();
                    }
                });
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
                            Swal.fire({
                                title: '¡Felicidades!',
                                text: 'Colocaste todas las cartas, ¡haz ganado!',
                                imageUrl: 'images/youWin.gif',
                                imageWidth: 400,
                                imageHeight: 200,
                                imageAlt: 'Custom image',
                            })
                        }
                    } else {
                        Swal.fire('No puedes colocar esta carta, revisa el orden.');
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

        function showCardsInPiles() {
            for (var i = 0; i < 4; i++) {
                var pile = document.getElementById(`pile_${i}`);
                console.log(`Pila ${i + 1}: ${pile.innerText.trim()}`);
            }
        }

        function mostrarCartasRestantesEnMazo() {
            var cartasRestantes = mazo.length;
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
            console.log('Estado de mano ', estadoMano);
            return estadoMano;
        }

        function guardarEstadoJuego() {
            console.log('Guardando estado del juego...');

            var gameState = {
                mazo: mazo,
                cartasColocadasTotales: cartasColocadasTotales,
                cartasColocadas: cartasColocadas,
                estadoTablero: obtenerEstadoTablero(),
                estadoMano: obtenerEstadoMano(),
                semilla: <?php echo $semilla; ?>,
            };

            localStorage.setItem('gameState', JSON.stringify(gameState));
            verificarPerdida();
        }

        function salirPorquePerdiste() {
            if (salirperdiste) {
                localStorage.removeItem('gameState');
                window.history.back();
            } else {
                guardarEstadoJuego();
            }
        }


        window.addEventListener('beforeunload', function(event) {
            salirPorquePerdiste();
        });

        function restaurarEstadoJuego() {
            console.log('Restaurando estado del juego...');
            var gameState = localStorage.getItem('gameState');

            if (gameState) {
                gameState = JSON.parse(gameState);

                mazo = gameState.mazo;
                cartasColocadasTotales = gameState.cartasColocadasTotales;
                cartasColocadas = gameState.cartasColocadas;
                var semilla = gameState.semilla;
                $semilla = semilla;
                // Restaurar el estado del tablero
                var estadoTablero = gameState.estadoTablero;
                for (var i = 0; i < estadoTablero.length; i++) {
                    var pile = document.getElementById(`pile_${i}`);
                    pile.innerHTML = '';
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
                manoDiv.innerHTML = '';
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

</html>