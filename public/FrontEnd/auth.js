export function checkAuth() {
    const token = localStorage.getItem('jwt_token');
    if (!token) {
        localStorage.setItem('redirect_after_login', window.location.href);
        
        const currentPath = window.location.pathname;
        const basePath = currentPath.substring(0, currentPath.lastIndexOf('/'));
        window.location.href = `${basePath}/login.html`;
    }
}