fetch('loadCategories.php')
  .then(res => res.json())
  .then(data => {
    const list = document.getElementById('category-list');
    list.innerHTML = '';
    data.forEach(cat => {
      const li = document.createElement('li');
      li.innerHTML = `${cat.name} 
        <a href="editCategory.php?id=${cat.id}">Edit</a> | 
        <a href="deleteCategory.php?id=${cat.id}">Delete</a>`;
      list.appendChild(li);
    });
  });
