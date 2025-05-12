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

export async function useSpell(spellId, targetCardPos) {
  const response = await fetch(`${API_BASE_URL}/use-spell`, {
    method: 'POST',
    headers: getAuthHeaders(),
    body: JSON.stringify({ spellId, targetCardPos })
  });
  if (!response.ok) {
    throw new Error('Erreur lors de l’utilisation du sort');
  }
}