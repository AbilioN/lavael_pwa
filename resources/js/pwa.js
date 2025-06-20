// PWA Management
class PWAManager {
    constructor() {
        this.swRegistration = null;
        this.isOnline = navigator.onLine;
        this.init();
    }

    async init() {
        this.setupEventListeners();
        await this.registerServiceWorker();
        this.checkForUpdates();
    }

    setupEventListeners() {
        // Online/Offline status
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.showNotification('Conexão restaurada!', 'success');
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.showNotification('Você está offline', 'warning');
        });

        // Before install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallPrompt();
        });

        // App installed
        window.addEventListener('appinstalled', () => {
            this.showNotification('Aplicação instalada com sucesso!', 'success');
            this.deferredPrompt = null;
        });
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                this.swRegistration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker registrado:', this.swRegistration);

                // Verificar atualizações
                this.swRegistration.addEventListener('updatefound', () => {
                    const newWorker = this.swRegistration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateNotification();
                        }
                    });
                });

            } catch (error) {
                console.error('Erro ao registrar Service Worker:', error);
            }
        }
    }

    showInstallPrompt() {
        const installButton = document.createElement('div');
        installButton.innerHTML = `
            <div id="install-prompt" style="
                position: fixed;
                bottom: 20px;
                left: 20px;
                right: 20px;
                background: #f53003;
                color: white;
                padding: 16px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                display: flex;
                justify-content: space-between;
                align-items: center;
            ">
                <div>
                    <strong>Instalar App</strong>
                    <p style="margin: 4px 0 0 0; font-size: 14px; opacity: 0.9;">
                        Adicione esta aplicação à sua tela inicial
                    </p>
                </div>
                <div>
                    <button onclick="pwaManager.installApp()" style="
                        background: white;
                        color: #f53003;
                        border: none;
                        padding: 8px 16px;
                        border-radius: 4px;
                        font-weight: 600;
                        cursor: pointer;
                        margin-right: 8px;
                    ">Instalar</button>
                    <button onclick="pwaManager.dismissInstallPrompt()" style="
                        background: transparent;
                        color: white;
                        border: 1px solid rgba(255,255,255,0.3);
                        padding: 8px 16px;
                        border-radius: 4px;
                        cursor: pointer;
                    ">Agora não</button>
                </div>
            </div>
        `;
        document.body.appendChild(installButton);
    }

    async installApp() {
        if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                console.log('Usuário aceitou a instalação');
            } else {
                console.log('Usuário recusou a instalação');
            }
            this.deferredPrompt = null;
        }
        this.dismissInstallPrompt();
    }

    dismissInstallPrompt() {
        const prompt = document.getElementById('install-prompt');
        if (prompt) {
            prompt.remove();
        }
    }

    showUpdateNotification() {
        const updateButton = document.createElement('div');
        updateButton.innerHTML = `
            <div id="update-notification" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: #28a745;
                color: white;
                padding: 12px 16px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                display: flex;
                align-items: center;
                gap: 12px;
            ">
                <span>Nova versão disponível!</span>
                <button onclick="pwaManager.updateApp()" style="
                    background: white;
                    color: #28a745;
                    border: none;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 12px;
                    cursor: pointer;
                ">Atualizar</button>
                <button onclick="pwaManager.dismissUpdateNotification()" style="
                    background: transparent;
                    color: white;
                    border: none;
                    cursor: pointer;
                    font-size: 18px;
                ">&times;</button>
            </div>
        `;
        document.body.appendChild(updateButton);
    }

    updateApp() {
        if (this.swRegistration && this.swRegistration.waiting) {
            this.swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
        }
        this.dismissUpdateNotification();
    }

    dismissUpdateNotification() {
        const notification = document.getElementById('update-notification');
        if (notification) {
            notification.remove();
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? '#28a745' : type === 'warning' ? '#ffc107' : '#17a2b8';
        
        notification.innerHTML = `
            <div style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${bgColor};
                color: white;
                padding: 12px 16px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                max-width: 300px;
                animation: slideIn 0.3s ease;
            ">
                ${message}
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove após 3 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    checkForUpdates() {
        // Verificar atualizações a cada hora
        setInterval(() => {
            if (this.swRegistration) {
                this.swRegistration.update();
            }
        }, 60 * 60 * 1000);
    }

    // Método para solicitar permissão de notificação
    async requestNotificationPermission() {
        if ('Notification' in window) {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                this.showNotification('Notificações ativadas!', 'success');
            }
            return permission;
        }
        return 'denied';
    }

    // Método para enviar notificação
    sendNotification(title, options = {}) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const defaultOptions = {
                icon: '/icons/icon-192x192.png',
                badge: '/icons/icon-72x72.png',
                vibrate: [100, 50, 100],
                ...options
            };
            
            new Notification(title, defaultOptions);
        }
    }
}

// Inicializar PWA Manager
const pwaManager = new PWAManager();

// Adicionar estilos CSS para animações
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

// Exportar para uso global
window.pwaManager = pwaManager; 