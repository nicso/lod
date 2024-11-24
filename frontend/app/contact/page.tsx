
'use client';
import { useState } from 'react';

export default function TestPage() {
  const [message, setMessage] = useState<string>('');

  const fetchMessage = async () => {
    try {
      console.log('Fetching message...'); // Pour le debug
      const response = await fetch('http://localhost:8000/api/index.php', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
        },
      });
      console.log('Response:', response); // Pour le debug
      const data = await response.json();
      console.log('Data:', data); // Pour le debug
      setMessage(data.message);
    } catch (error) {
      console.error('Erreur:', error);
      setMessage('Erreur lors de la récupération du message');
    }
  };

  return (
    <div className="min-h-screen flex flex-col items-center justify-center p-4">
      <button 
        onClick={fetchMessage}
        className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
      >
        Obtenir le message
      </button>
      
      {message && (
        <div className="mt-4 p-4 bg-gray-100 rounded">
          {message}
        </div>
      )}

      <div className="mt-4 text-sm text-gray-500">
        Vérifiez la console du navigateur pour les logs
      </div>
    </div>
  );
}