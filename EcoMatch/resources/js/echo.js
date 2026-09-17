import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export function initEcho(reverbConfig) {
    if (typeof window !== 'undefined' && reverbConfig && reverbConfig.key) {
        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: reverbConfig.key,
            wsHost: reverbConfig.host,
            wsPort: reverbConfig.port ?? 80,
            wssPort: reverbConfig.port ?? 443,
            forceTLS: (reverbConfig.scheme ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
        
        console.log('Reverb inicializado correctamente con la key:', reverbConfig.key);
    } else {
        console.warn('No se pudo inicializar Reverb: Falta la configuración.');
    }
}