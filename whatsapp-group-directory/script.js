// Sample Data (In real website, this would come from backend)
const categories = [
    { id: 1, name: 'Study Group', icon: '📚', count: 45 },
    { id: 2, name: 'Business', icon: '💼', count: 38 },
    { id: 3, name: 'Cricket', icon: '🏏', count: 27 },
    { id: 4, name: 'Technology', icon: '💻', count: 52 },
    { id: 5, name: 'Entertainment', icon: '🎬', count: 63 },
    { id: 6, name: 'Jobs Alert', icon: '🔔', count: 41 },
    { id: 7, name: 'Local Groups', icon: '📍', count: 34 },
    { id: 8, name: 'Health & Fitness', icon: '💪', count: 19 },
    { id: 9, name: 'Cooking', icon: '🍳', count: 23 },
    { id: 10, name: 'Travel', icon: '✈️', count: 17 }
];

const groups = [
    {
        id: 1,
        name: 'UPSC 2026 Aspirants',
        category: 'Study Group',
        categoryId: 1,
        description: 'Daily current affairs, notes sharing, and discussion for UPSC preparation',
        members: 1250,
        views: 5400,
        link: 'https://chat.whatsapp.com/example1',
        featured: true,
        language: 'Hindi',
        date: '2026-02-20'
    },
    {
        id: 2,
        name: 'Digital India Business',
        category: 'Business',
        categoryId: 2,
        description: 'Business ideas, marketing tips, and networking',
        members: 890,
        views: 3200,
        link: 'https://chat.whatsapp.com/example2',
        featured: true,
        language: 'Hinglish',
        date: '2026-02-21'
    },
    {
        id: 3,
        name: 'IPL 2026 Fans',
        category: 'Cricket',
        categoryId: 3,
        description: 'IPL updates, match discussions, fantasy league',
        members: 2100,
        views: 8900,
        link: 'https://chat.whatsapp.com/example3',
        featured: true,
        language: 'Hindi',
        date: '2026-02-21'
    },
    {
        id: 4,
        name: 'Web Developers India',
        category: 'Technology',
        categoryId: 4,
        description: 'HTML, CSS, JavaScript, React - Learn and grow together',
        members: 750,
        views: 2800,
        link: 'https://chat.whatsapp.com/example4',
        featured: false,
        language: 'English',
        date: '2026-02-19'
    },
    {
        id: 5,
        name: 'Govt Job Alerts',
        category: 'Jobs Alert',
        categoryId: 6,
        description: 'Sarkari naukri notifications, exam tips',
        members: 3400,
        views: 12500,
        link: 'https://chat.whatsapp.com/example5',
        featured: false,
        language: 'Hindi',
        date: '2026-02-20'
    },
    {
        id: 6,
        name: 'Mumbai Local Group',
        category: 'Local Groups',
        categoryId: 7,
        description: 'Mumbai events, flatmates, local business',
        members: 560,
        views: 1900,
        link: 'https://chat.whatsapp.com/example6',
        featured: false,
        language: 'Hindi',
        date: '2026-02-18'
    }
];

// Local Storage for submitted groups
let submittedGroups = JSON.parse(localStorage.getItem('submittedGroups')) || [];

// Initialize Website
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadFeaturedGroups();
    loadLatestGroups();
    loadCategoriesGrid();
    loadFooterCategories();
    setupMobileMenu();
});

// Load Categories in Pills
function loadCategories() {
    const pillsContainer = document.getElementById('categoryPills');
    pillsContainer.innerHTML = '';
    
    categories.slice(0, 8).forEach(cat => {
        const pill = document.createElement('button');
        pill.className = 'pill';
        pill.innerHTML = `${cat.icon} ${cat.name}`;
        pill.onclick = () => filterByCategory(cat.id);
        pillsContainer.appendChild(pill);
    });
}

// Load Featured Groups
function loadFeaturedGroups() {
    const container = document.getElementById('featuredGroups');
    container.innerHTML = '';
    
    const featured = groups.filter(g => g.featured).slice(0, 3);
    
    featured.forEach(group => {
        container.appendChild(createGroupCard(group));
    });
}

// Load Latest Groups
function loadLatestGroups() {
    const container = document.getElementById('latestGroups');
    container.innerHTML = '';
    
    // Combine original groups with submitted groups
    const allGroups = [...groups, ...submittedGroups];
    const latest = allGroups.sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, 6);
    
    latest.forEach(group => {
        container.appendChild(createGroupCard(group));
    });
}

// Create Group Card HTML
function createGroupCard(group) {
    const card = document.createElement('div');
    card.className = 'group-card';
    
    const date = new Date(group.date);
    const timeAgo = getTimeAgo(date);
    
    card.innerHTML = `
        <div class="card-header">
            <span class="category-badge">${getCategoryIcon(group.categoryId)} ${group.category}</span>
            ${group.featured ? '<span class="featured-badge"><i class="fas fa-crown"></i> Featured</span>' : ''}
        </div>
        <h3>${group.name}</h3>
        <p class="group-desc">${group.description.substring(0, 80)}${group.description.length > 80 ? '...' : ''}</p>
        <div class="group-meta">
            <span><i class="fas fa-users"></i> ${group.members}+</span>
            <span><i class="fas fa-eye"></i> ${group.views}</span>
            <span><i class="fas fa-clock"></i> ${timeAgo}</span>
        </div>
        <div class="card-actions">
            <button class="btn-view" onclick="viewGroup(${group.id})">View Details</button>
            <a href="${group.link}" target="_blank" class="btn-join" onclick="trackClick(${group.id})">
                <i class="fab fa-whatsapp"></i> Join
            </a>
        </div>
    `;
    
    return card;
}

// Get Category Icon
function getCategoryIcon(categoryId) {
    const cat = categories.find(c => c.id === categoryId);
    return cat ? cat.icon : '📌';
}

// Load Categories Grid
function loadCategoriesGrid() {
    const container = document.getElementById('categoriesGrid');
    container.innerHTML = '';
    
    categories.forEach(cat => {
        const card = document.createElement('button');
        card.className = 'category-card';
        card.onclick = () => filterByCategory(cat.id);
        card.innerHTML = `
            <div class="category-icon">${cat.icon}</div>
            <h3>${cat.name}</h3>
            <p>${cat.count} Groups</p>
        `;
        container.appendChild(card);
    });
}

// Load Footer Categories
function loadFooterCategories() {
    const container = document.getElementById('footerCategories');
    container.innerHTML = '';
    
    categories.slice(0, 5).forEach(cat => {
        const li = document.createElement('li');
        li.innerHTML = `<a href="#" onclick="filterByCategory(${cat.id})">${cat.name}</a>`;
        container.appendChild(li);
    });
}

// Filter by Category
function filterByCategory(categoryId) {
    const cat = categories.find(c => c.id === categoryId);
    alert(`Showing all ${cat.name} groups! (This would filter in real implementation)`);
    // In real implementation, you would redirect to category page or filter current view
}

// Search Groups
function searchGroups() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    if (query.trim() === '') {
        alert('Please enter a search term');
        return;
    }
    
    const allGroups = [...groups, ...submittedGroups];
    const results = allGroups.filter(g => 
        g.name.toLowerCase().includes(query) || 
        g.description.toLowerCase().includes(query) ||
        g.category.toLowerCase().includes(query)
    );
    
    if (results.length > 0) {
        alert(`Found ${results.length} groups matching "${query}"`);
        // In real implementation, you would show search results page
    } else {
        alert(`No groups found matching "${query}"`);
    }
}

// Submit Group Form
function submitGroup(event) {
    event.preventDefault();
    
    const groupName = document.getElementById('group_name').value;
    const link = document.getElementById('whatsapp_link').value;
    const categoryId = parseInt(document.getElementById('category').value);
    const language = document.getElementById('language').value;
    const description = document.getElementById('description').value;
    const terms = document.getElementById('terms').checked;
    
    // Validate WhatsApp link
    if (!link.includes('chat.whatsapp.com')) {
        showFormMessage('Please enter a valid WhatsApp group link', 'error');
        return;
    }
    
    if (!terms) {
        showFormMessage('Please agree to the terms', 'error');
        return;
    }
    
    // Get category name
    const category = categories.find(c => c.id === categoryId);
    
    // Create new group object
    const newGroup = {
        id: groups.length + submittedGroups.length + 1,
        name: groupName,
        category: category.name,
        categoryId: categoryId,
        description: description || 'No description provided',
        members: 0,
        views: 0,
        link: link,
        featured: false,
        language: language,
        date: new Date().toISOString().split('T')[0]
    };
    
    // Save to localStorage
    submittedGroups.push(newGroup);
    localStorage.setItem('submittedGroups', JSON.stringify(submittedGroups));
    
    // Show success message
    showFormMessage('Group submitted successfully! It will appear after admin approval.', 'success');
    
    // Reset form
    document.getElementById('groupForm').reset();
    
    // Reload latest groups
    loadLatestGroups();
}

// Show Form Message
function showFormMessage(message, type) {
    const msgDiv = document.getElementById('formMessage');
    msgDiv.style.display = 'block';
    msgDiv.className = `alert ${type}`;
    msgDiv.textContent = message;
    
    setTimeout(() => {
        msgDiv.style.display = 'none';
    }, 5000);
}

// Track Click
function trackClick(groupId) {
    console.log(`Group ${groupId} clicked - track for analytics`);
    // In real implementation, you would send analytics data
}

// View Group Details
function viewGroup(groupId) {
    const allGroups = [...groups, ...submittedGroups];
    const group = allGroups.find(g => g.id === groupId);
    alert(`Group: ${group.name}\nLink: ${group.link}\nMembers: ${group.members}+`);
    // In real implementation, you would open group detail page
}

// Get Time Ago
function getTimeAgo(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 60) return `${diffMins} mins ago`;
    if (diffHours < 24) return `${diffHours} hours ago`;
    if (diffDays < 7) return `${diffDays} days ago`;
    return date.toLocaleDateString();
}

// Load Categories in Submit Form
function loadCategoryOptions() {
    const select = document.getElementById('category');
    if (select) {
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = `${cat.icon} ${cat.name}`;
            select.appendChild(option);
        });
    }
}

// Setup Mobile Menu
function setupMobileMenu() {
    const menuBtn = document.querySelector('.mobile-menu');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuBtn) {
        menuBtn.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
            navLinks.style.flexDirection = 'column';
            navLinks.style.position = 'absolute';
            navLinks.style.top = '70px';
            navLinks.style.left = '0';
            navLinks.style.width = '100%';
            navLinks.style.background = 'white';
            navLinks.style.padding = '20px';
            navLinks.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        });
    }
}

// Initialize category options when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCategoryOptions();
});

// For category page functionality (if you create separate category.html)
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// If on category page, load category groups
if (window.location.pathname.includes('category.html')) {
    const catId = getQueryParam('id');
    if (catId) {
        loadCategoryGroups(parseInt(catId));
    }
}

function loadCategoryGroups(categoryId) {
    const category = categories.find(c => c.id === categoryId);
    if (category) {
        document.getElementById('categoryTitle').textContent = `${category.icon} ${category.name} Groups`;
        
        const allGroups = [...groups, ...submittedGroups];
        const catGroups = allGroups.filter(g => g.categoryId === categoryId);
        
        // Display groups...
    }
}