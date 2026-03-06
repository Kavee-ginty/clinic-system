function showToast(message, type = 'success') {
    // Check if the container exists
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-5 right-5 z-50 flex flex-col gap-3';
        document.body.appendChild(container);
    }

    // Create the toast element
    const toast = document.createElement('div');
    
    // Set styles based on type
    const baseClasses = 'min-w-[250px] shadow-lg rounded-lg p-4 font-bold text-sm transform transition-all duration-300 translate-x-32 opacity-0 flex items-center justify-between gap-4 border-l-4';
    
    const colors = {
        success: 'bg-white border-green-500 text-gray-800',
        error: 'bg-white border-red-500 text-gray-800',
        info: 'bg-teal-800 text-white border-teal-500' // Darker styles for info
    };
    
    // Icons based on type
    const icons = {
        success: '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
        error: '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
        info: '<svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
    };

    toast.className = `${baseClasses} ${colors[type]}`;
    toast.innerHTML = `
        <div class="flex items-center gap-2 overflow-hidden">
            ${icons[type]}
            <span class="break-words">${message}</span>
        </div>
        <button class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-0 ml-2 font-black self-start" onclick="this.parentElement.remove()">&times;</button>
    `;

    // Append to container
    container.appendChild(toast);

    // Trigger animation in
    setTimeout(() => {
        toast.classList.remove('translate-x-32', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 10);

    // Auto remove after 3.5s
    setTimeout(() => {
        if (container.contains(toast)) {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-32', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }
    }, 3500);
}
