import { checkAuth } from './auth.js';
import { fetchActiveGame, useSpell } from './api.js';

checkAuth();

const spellContainer = document.getElementById('spells-container');
const buttonContainer = document.getElementById("spell-buttons-container");
const validateButton = document.getElementById('spell-button-validate');
const cancelButton = document.getElementById('spell-button-cancel');
const token = localStorage.getItem('jwt_token');
const username = jwt_decode(token).username;
let selectedSpell = null;
let selectedTargetCard = null;
let selectedCard = null;
let selectedActions = [];

loadActiveGame();

async function loadActiveGame() {
  try {
    const activeGame = await fetchActiveGame();
    player1Board.innerHTML = "";
    player2Board.innerHTML = "";
    clearCardSelection();
    activeGame.activeCards.sort((a, b) => a.position - b.position).forEach(activeCard => {
      if (activeCard.user.username === username) {
        addActiveCardWithHP(activeCard, player1Board, true);
      } else {
        addActiveCardWithHP(activeCard, player2Board, false);
      }
    });

  } catch (error) {
    console.error(error);
  }
}

function addActiveCardWithHP(activeCard, container, isUserCard) {
  const imageWrapper = document.createElement("div");
  imageWrapper.classList.add("image-wrapper");
  imageWrapper.dataset.cardId = activeCard.id;

  const img = document.createElement("img");
  img.src = activeCard.card.link;
  img.classList.add("image");

  const heartContainer = document.createElement("div");
  heartContainer.classList.add("heart-container");
  heartContainer.innerHTML = `
        <svg class="heart-svg" viewBox="0 0 24 24">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="red"/>
            <text x="50%" y="55%" text-anchor="middle" dominant-baseline="middle" class="heart-text">${activeCard.currentHp}</text>
        </svg>
    `;

  imageWrapper.addEventListener("click", function () {
    if (selectedSpell) {
      const targetType = selectedSpell.spellEffects[0]?.target;

      const isTargetAlly = targetType === "ally";
      const isTargetEnemy = targetType === "ennemy";

      if ((isTargetAlly && isUserCard) || (isTargetEnemy && !isUserCard)) {
        selectedTargetCard = activeCard;

        document.querySelectorAll(".image-wrapper").forEach(card => card.classList.remove("selected-target"));
        imageWrapper.classList.add("selected-target");
      } else {
        alert("Ce sort ne peut pas être utilisé sur cette cible.");
      }
    } else if (isUserCard) {
      selectedCard = activeCard;
      document.querySelectorAll(".image-wrapper").forEach(card => card.classList.remove("selected"));
      imageWrapper.classList.add("selected");
      showSpellsForCard(activeCard.activeSpells, false);
    }
  });

  imageWrapper.appendChild(img);
  imageWrapper.appendChild(heartContainer);
  container.appendChild(imageWrapper);

}

function showSpellsForCard(activeSpells) {
  spellContainer.innerHTML = "";
  selectedSpell = null;

  activeSpells.forEach(spell => {
    const spellImg = document.createElement("img");
    spellImg.src = spell.link;
    spellImg.classList.add("spell-image");
    spellImg.dataset.spellId = spell.id;

    spellImg.addEventListener("click", () => {
      clearCardSelection();
      selectedSpell = spell;
      spellImg.classList.add("selected");
      showValidateButton();
    });

    spellContainer.appendChild(spellImg);
    buttonContainer.appendChild(validateButton);
    buttonContainer.appendChild(cancelButton);
    spellContainer.appendChild(buttonContainer);
  });
}

function showValidateButton() {
  validateButton.onclick = () => {
    if (selectedSpell && selectedTargetCard) {
      const existingIndex = selectedActions.findIndex(action => action.selectedCard == selectedCard);

      const newAction = {
        selectedCard: selectedCard,
        selectedSpell: selectedSpell,
        selectedTargetCard: selectedTargetCard,
      };

      if (existingIndex !== -1) {
        selectedActions[existingIndex] = newAction;
      } else {
        selectedActions.push(newAction);
      }

      clearCardSelection();

      if (selectedActions.length >= 1) {
        showConfirmTurnButton();
      }

      updateActionSummary();
    } else {
      alert("Sélectionne un sort et une cible !");
    }
  };
  cancelButton.onclick = () => {
    clearCardSelection();
  }
}

function updateActionSummary() {
  const summaryContainer = document.getElementById("action-summary");
  summaryContainer.innerHTML = "";

  selectedActions.forEach((action) => {
    const entry = document.createElement("div");
    entry.classList.add("action-entry");

    const casterImg = document.createElement("img");
    casterImg.src = action.selectedCard.card.link;
    entry.appendChild(casterImg);

    const spellImg = document.createElement("img");
    spellImg.src = action.selectedSpell.link;
    entry.appendChild(spellImg);

    const targetImg = document.createElement("img");
    targetImg.src = action.selectedTargetCard.card.link;
    entry.appendChild(targetImg);

    entry.addEventListener("click", () => {
      clearCardSelection();

      selectedCard = action.selectedCard;
      showSpellsForCard(selectedCard.activeSpells);

      selectedTargetCard = action.selectedTargetCard;
      selectedSpell = action.selectedSpell;

      document.querySelectorAll(".image-wrapper").forEach(card => card.classList.remove("selected"));
      document.querySelector(`[data-card-id='${action.selectedCard.id}']`)?.classList.add("selected");
      document.querySelector(`[data-spell-id='${action.selectedSpell.id}']`)?.classList.add("selected");
      document.querySelector(`[data-card-id='${action.selectedTargetCard.id}']`)?.classList.add("selected-target");
    });

    summaryContainer.appendChild(entry);
  });
}

function showConfirmTurnButton() {
  if (document.getElementById("confirm-turn-button")) return;

  const confirmTurnButton = document.createElement("button");
  confirmTurnButton.id = "confirm-turn-button";
  confirmTurnButton.innerText = "Confirmer le tour";
  confirmTurnButton.classList.add("spell-button");

  buttonContainer.appendChild(confirmTurnButton);

  confirmTurnButton.onclick = async () => {

    selectedActions.sort((a, b) => {
      if (b.selectedCard.card.speed !== a.selectedCard.card.speed) {
        return b.selectedCard.card.speed - a.selectedCard.card.speed;
      }
      return a.selectedCard.currentHp - b.selectedCard.currentHp;
    });

    try {
      for (const action of selectedActions) {
        await useSpell(action.selectedSpell.id, action.selectedTargetCard.position);
      }
    } catch (error) {
      console.error(error);
    }


    selectedActions = [];
    confirmTurnButton.remove();
    document.getElementById("action-summary").innerHTML = "";
    loadActiveGame();
  };
}

function clearCardSelection() {
  selectedSpell = null;
  selectedTargetCard = null;
  document.querySelectorAll(".image-wrapper").forEach(card => card.classList.remove("selected-target"));
  document.querySelectorAll(".spell-image").forEach(img => img.classList.remove("selected"));
}