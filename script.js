// Global variables
let categories = [];
let allGroups = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Website loaded, connecting to Firebase...');
    
    // Load categories
    loadCategories();
    
    // Load featured groups
    loadFeaturedGroups();
    
    // Load latest groups
    loadLatestGroups();
});

// Load categories from Firebase
function loadCategories() {
    database.ref('categories').on('value', (snapshot) => {
        categories = [];
        snapshot.forEach((childSnapshot) => {
            categories.push({
                id: childSnapshot.key,
                ...childSnapshot.val()
            });
        });
        
        // Update UI
        displayCategoryPills();
        displayCategoriesGrid();
        displayFooterCategories();
        populateCategorySelect();
    });
}

// Display category pills
function displayCategoryPills() {
    const container = document.getElementById('categoryPills');
    if (!container) return;
    
    container.innerHTML = '';
    
    categories.slice(0, 8).forEach(cat => {
        const pill = document.createElement('button');
        pill.className = 'pill';
        pill.innerHTML = `${cat.icon || '📌'} ${cat.name}`;
        pill.onclick = () => filterByCategory(cat.id);
        container.appendChild(pill);
    });
}

// Display categories grid
function displayCategoriesGrid() {
    const container = document.getElementById('categoriesGrid');
    if (!container) return;
    
    container.innerHTML = '';
    
    categories.forEach(cat => {
        const count = allGroups.filter(g => g.categoryId === cat.id && g.status === 'approved').length;
        
        const card = document.createElement('button');
        card.className = 'category-card';
        card.onclick = () => filterByCategory(cat.id);
        card.innerHTML = `
            <div class="category-icon">${cat.icon || '📌'}</div>
            <h3>${cat.name}</h3>
            <p>${count} Groups</p>
        `;
        container.appendChild(card);
    });
}

// Display footer categories
function displayFooterCategories() {
    const container = document.getElementById('footerCategories');
    if (!container) return;
    
    container.innerHTML = '';
    
    categories.slice(0, 5).forEach(cat => {
        const li = document.createElement('li');
        li.innerHTML = `<a href="#" onclick="filterByCategory('${cat.id}')">${cat.name}</a>`;
        container.appendChild(li);
    });
}

// Populate category select
function populateCategorySelect() {
    const select = document.getElementById('category');
    if (!select) return;
    
    select.innerHTML = '<option value="">Select Category</option>';
    
    categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = `${cat.icon || '📌'} ${cat.name}`;
        select.appendChild(option);
    });
}

// Load featured groups
function loadFeaturedGroups() {
    database.ref('groups').orderByChild('featured').equalTo(true).limitToFirst(4).on('value', (snapshot) => {
        const featuredGroups = [];
        snapshot.forEach((childSnapshot) => {
            featuredGroups.push({
                id: childSnapshot.key,
                ...childSnapshot.val()
            });
        });
        
        displayFeaturedGroups(featuredGroups);
    });
}

// Display featured groups
function displayFeaturedGroups(groups) {
    const container = document.getElementById('featuredGroups');
    if (!container) return;
    
    if (groups.length === 0) {
        container.innerHTML = '<div class="no-groups">No featured groups yet</div>';
        return;
    }
    
    container.innerHTML = '';
    
    groups.forEach(group => {
        if (group.status !== 'approved') return;
        
        const card = createGroupCard(group);
        container.appendChild(card);
    });
}

// Load latest groups
function loadLatestGroups() {
    database.ref('groups').orderByChild('createdAt').limitToLast(8).on('value', (snapshot) => {
        const latestGroups = [];
        snapshot.forEach((childSnapshot) => {
            latestGroups.push({
                id: childSnapshot.key,
                ...childSnapshot.val()
            });
        });
        
        latestGroups.reverse();
        displayLatestGroups(latestGroups);
        allGroups = latestGroups;
    });
}

// Display latest groups
function displayLatestGroups(groups) {
    const container = document.getElementById('latestGroups');
    if (!container) return;
    
    const approvedGroups = groups.filter(g => g.status === 'approved').slice(0, 6);
    
    if (approvedGroups.length === 0) {
        container.innerHTML = '<div class="no-groups">No groups yet. Be the first to submit!</div>';
        return;
    }
    
    container.innerHTML = '';
    
    approvedGroups.forEach(group => {
        const card = createGroupCard(group);
        container.appendChild(card);
    });
}

// Create group card
function createGroupCard(group) {
    const card = document.createElement('div');
    card.className = 'group-card';
    
    const category = categories.find(c => c.id === group.categoryId);
    const categoryIcon = category ? category.icon : '📌';
    
    const date = group.createdAt ? new Date(group.createdAt) : new Date();
    const timeAgo = getTimeAgo(date);
    
    card.innerHTML = `
        <div class="card-header">
            <span class="category-badge">${categoryIcon} ${group.category || 'General'}</span>
            ${group.featured ? '<span class="featured-badge"><i class="fas fa-crown"></i> Featured</span>' : ''}
        </div>
        <h3>${group.name}</h3>
        <p class="group-desc">${(group.description || 'No description').substring(0, 80)}${group.description && group.description.length > 80 ? '...' : ''}</p>
        <div class="group-meta">
            <span><i class="fas fa-users"></i> ${group.members || 0}+</span>
            <span><i class="fas fa-eye"></i> ${group.views || 0}</span>
            <span><i class="fas fa-clock"></i> ${timeAgo}</span>
        </div>
        <div class="card-actions">
            <button class="btn-view" onclick="viewGroup('${group.id}')">View Details</button>
            <a href="${group.link}" target="_blank" class="btn-join" onclick="incrementViews('${group.id}')">
                <i class="fab fa-whatsapp"></i> Join
            </a>
        </div>
    `;
    
    return card;
}

// Submit group
function submitGroup(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    const categoryId = document.getElementById('category').value;
    const category = categories.find(c => c.id === categoryId);
    
    if (!category) {
        showFormMessage('Please select a category', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Group';
        return;
    }
    
    const groupData = {
        name: document.getElementById('group_name').value,
        link: document.getElementById('whatsapp_link').value,
        categoryId: categoryId,
        category: category.name,
        language: document.getElementById('language').value,
        description: document.getElementById('description').value || '',
        members: 0,
        views: 0,
        featured: false,
        status: 'pending',
        submittedBy: 'user',
        submitterEmail: document.getElementById('submitter_email').value || 'anonymous',
        createdAt: new Date().toISOString()
    };
    
    if (!groupData.link.includes('chat.whatsapp.com')) {
        showFormMessage('Please enter a valid WhatsApp group link', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Group';
        return;
    }
    
    const newGroupRef = database.ref('groups').push();
    groupData.id = newGroupRef.key;
    
    newGroupRef.set(groupData)
        .then(() => {
            showFormMessage('Group submitted successfully! It will appear after admin approval.', 'success');
            document.getElementById('groupForm').reset();
        })
        .catch((error) => {
            showFormMessage('Error: ' + error.message, 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Group';
        });
}

// Show form message
function showFormMessage(message, type) {
    const msgDiv = document.getElementById('formMessage');
    msgDiv.style.display = 'block';
    msgDiv.className = `alert ${type}`;
    msgDiv.textContent = message;
    
    setTimeout(() => {
        msgDiv.style.display = 'none';
    }, 5000);
}

// Increment views
function incrementViews(groupId) {
    const groupRef = database.ref('groups/' + groupId);
    groupRef.transaction((group) => {
        if (group) {
            group.views = (group.views || 0) + 1;
        }
        return group;
    });
}

// View group details
function viewGroup(groupId) {
    database.ref('groups/' + groupId).once('value', (snapshot) => {
        const group = snapshot.val();
        if (group) {
            alert(`Group: ${group.name}\nCategory: ${group.category}\nMembers: ${group.members || 0}\n\nDescription: ${group.description || 'No description'}`);
        }
    });
}

// Filter by category
function filterByCategory(categoryId) {
    const category = categories.find(c => c.id === categoryId);
    if (category) {
        alert(`Showing all ${category.name} groups! (Category page coming soon)`);
    }
}

// Search groups
function searchGroups() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    if (!query) {
        alert('Please enter a search term');
        return;
    }
    
    database.ref('groups').orderByChild('status').equalTo('approved').once('value', (snapshot) => {
        const results = [];
        snapshot.forEach((childSnapshot) => {
            const group = childSnapshot.val();
            if (group.name.toLowerCase().includes(query) || 
                (group.description && group.description.toLowerCase().includes(query))) {
                results.push(group);
            }
        });
        
        alert(`Found ${results.length} groups matching "${query}"`);
    });
}

// Get time ago
function getTimeAgo(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    return date.toLocaleDateString();
}

// Mobile menu
document.querySelector('.mobile-menu')?.addEventListener('click', function() {
    const navLinks = document.querySelector('.nav-links');
    if (navLinks.style.display === 'flex') {
        navLinks.style.display = 'none';
    } else {
        navLinks.style.display = 'flex';
        navLinks.style.flexDirection = 'column';
        navLinks.style.position = 'absolute';
        navLinks.style.top = '70px';
        navLinks.style.left = '0';
        navLinks.style.width = '100%';
        navLinks.style.background = 'white';
        navLinks.style.padding = '20px';
        navLinks.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        navLinks.style.zIndex = '1000';
    }
});
