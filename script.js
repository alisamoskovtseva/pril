document.addEventListener('DOMContentLoaded', function() {
    const taskInput = document.getElementById('taskInput');
    const addTaskBtn = document.getElementById('addTaskBtn');
    const taskList = document.getElementById('taskList');
    
    // Загрузка задач из localStorage
    loadTasks();
    
    addTaskBtn.addEventListener('click', addTask);
    taskInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            addTask();
        }
    });
    
    function addTask() {
        const taskText = taskInput.value.trim();
        
        if (taskText) {
            // Создаем элемент задачи
            const taskItem = document.createElement('li');
            taskItem.innerHTML = `
                <span>${taskText}</span>
                <button class="delete-btn">Удалить</button>
            `;
            
            // Добавляем обработчик удаления
            const deleteBtn = taskItem.querySelector('.delete-btn');
            deleteBtn.addEventListener('click', function() {
                taskItem.remove();
                saveTasks();
            });
            
            // Добавляем в список
            taskList.appendChild(taskItem);
            
            // Очищаем поле ввода
            taskInput.value = '';
            
            // Сохраняем задачи
            saveTasks();
        }
    }
    
    function saveTasks() {
        const tasks = [];
        document.querySelectorAll('#taskList li span').forEach(task => {
            tasks.push(task.textContent);
        });
        localStorage.setItem('tasks', JSON.stringify(tasks));
    }
    
    function loadTasks() {
        const tasks = JSON.parse(localStorage.getItem('tasks')) || [];
        tasks.forEach(taskText => {
            const taskItem = document.createElement('li');
            taskItem.innerHTML = `
                <span>${taskText}</span>
                <button class="delete-btn">Удалить</button>
            `;
            
            const deleteBtn = taskItem.querySelector('.delete-btn');
            deleteBtn.addEventListener('click', function() {
                taskItem.remove();
                saveTasks();
            });
            
            taskList.appendChild(taskItem);
        });
    }
});