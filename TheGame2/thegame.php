<!DOCTYPE html>
<html>

<head>
    <title>The Game</title>
    <style>
        .card {
            width: 50px;
            height: 75px;
            border: 1px solid black;
            display: inline-block;
            margin: 5px;
            text-align: center;
            line-height: 75px;
            cursor: pointer;
        }

        .board {
            margin-bottom: 20px;
        }

        .hand {
            text-align: center;
        }

        .hand .card {
            width: 50px;
            height: 75px;
            font-size: 14px;
        }

        .pile {
            width: 100px;
            height: 125px;
            border: 1px solid black;
            display: inline-block;
            margin: 5px;
            text-align: center;
            line-height: 75px;
            cursor: pointer;
        }
    </style>
</head>
<?php
//Funciones de inicianicializacion
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

    <script>
        var intentosColocacion = 0;
        var mazo = <?php echo json_encode(array_map(function ($card) {
                        return array('number' => $card->getNumber());
                    }, $mazo)); ?>;
        console.log('Mazo en JavaScript:', mazo);
        var draggedCardId;
        var hand = [];
        var cartasColocadasTotales = 0; //Para checar si gané o no nomas cambie este a 97 y tire una carta :)
        var button = document.createElement('button');
        var cartasColocadas = 0;
        // Crea un elemento de botón
        button.innerText = 'Robar/Jalar'; // Establece el texto del botón
        // Agrega un evento al botón
        button.addEventListener('click', function() {
            mostrarCartasRestantesEnMazo();

            if (cartasColocadas >= 2) {
                drawCards(cartasColocadas);
                cartasColocadas = 0;
            } else {
                alert('Se deben colocar por lo menos 2 cartas antes');
            }
        });
        // Añade el botón al cuerpo del documento
        document.body.appendChild(button);

        // Define las reglas de colocación en las pilas
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

        function ordenarCartasEnMano() {
            var manoDiv = document.getElementById('hand');
            var cards = manoDiv.getElementsByClassName('card');
            var sortedCards = Array.from(cards).sort(function(a, b) {
                return parseInt(b.innerText) - parseInt(a.innerText);
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
                    var drawnCard = mazo.pop(); // Obtén la carta del mazo
                    hand.push(drawnCard); // Agrega la carta a la mano
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
                            alert('¡Felicidades! Has colocado todas las cartas. ¡Ganaste!');
                        }

                    } else {
                        alert('No se puede colocar esta carta según las reglas.');
                        console.log("No se puede colocar esta carta según las reglas.");
                    }
                }

                draggedCardId = null;
            }
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