const API_BASE_URL = '/api';

const getAuthHeaders = () => {
  const token = localStorage.getItem('jwt_token');
  return {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  };
};

export async function fetchActiveGame() {
  const response = await fetch(`${API_BASE_URL}/activeGame`, {
    method: 'GET',
    headers: getAuthHeaders()
  });
  if (!response.ok) {
    throw new Error('Erreur lors du chargement de la partie active');
  }
  return response.json();
}

export async function useSpell(activeSpellId, targetCardPos) {
  const response = await fetch(`${API_BASE_URL}/use-spell`, {
    method: 'POST',
    headers: getAuthHeaders(),
    body: JSON.stringify({ activeSpellId, targetCardPos })
  });
  if (!response.ok) {
    throw new Error('Erreur lors de l’utilisation du sort');
  }
}

export async function endTurn() {
  const response = await fetch(`${API_BASE_URL}/activeGame/end-turn`, {
    method: 'GET',
    headers: getAuthHeaders(),
  });
  if (!response.ok) {
    throw new Error('Erreur lors de la fin du tour');
  }
  return response.json();
}