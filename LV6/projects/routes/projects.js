const express = require('express');
const router = express.Router();
const Project = require('../models/Project');

// FORMA ZA KREIRANJE NOVOG PROJEKTA
router.get('/new', (req, res) => {
  res.render('projects/new');
});

// CREATE
router.post('/', async (req, res) => {
  try {
    let tasks = [];
    if (req.body.tasks) {
      tasks = Array.isArray(req.body.tasks) ? req.body.tasks : [req.body.tasks];
      tasks = tasks
        .filter(task => task.description && task.description.trim() !== '')
        .map(task => ({
          description: task.description,
          completed: false
        }));
    }
    await Project.create({
      ...req.body,
      tasks
    });
    res.redirect('/projects');
  } catch (err) {
    console.error(err);
    res.status(400).send(err.message);
  }
});

// READ ALL - POPIS PROJEKATA
router.get('/', async (req, res) => {
  const projects = await Project.find();
  res.render('projects/index', { projects });
});

// READ ONE - PRIKAZ JEDNOG PROJEKTA
router.get('/:id', async (req, res) => {
  const project = await Project.findById(req.params.id);
  res.render('projects/show', { project });
});

// FORMA ZA UREĐIVANJE
router.get('/:id/edit', async (req, res) => {
  const project = await Project.findById(req.params.id);
  res.render('projects/edit', { project });
});

// SPREMANJE IZMJENA
router.put('/:id', async (req, res) => {
  try {
    console.log('Update request body:', req.body);
    let tasks = [];
    if (req.body.tasks) {
      tasks = Array.isArray(req.body.tasks) ? req.body.tasks : [req.body.tasks];
      tasks = tasks
        .filter(task => task.description && task.description.trim() !== '')
        .map(task => ({
          description: task.description,
          completed: task.completed === 'on' || task.completed === true,
          completedAt: task.completed === 'on' || task.completed === true 
            ? (task.completedAt ? new Date(task.completedAt) : new Date()) 
            : null
        }));
    }

    const updateData = {
      ...req.body,
      tasks
    };

    const project = await Project.findByIdAndUpdate(req.params.id, updateData, {
      new: true,
      runValidators: true
    });

    if (!project) {
      console.log(`Project with ID ${req.params.id} not found`);
      return res.status(404).send('Project not found');
    }

    console.log(`Updated project: ${project.naziv}`);
    res.redirect('/projects');
  } catch (err) {
    console.error('Update error:', err);
    res.status(400).send(err.message);
  }
});

// BRISANJE PROJEKTA
router.delete('/:id', async (req, res) => {
  try {
    console.log(`DELETE request received for project ID: ${req.params.id}`);
    const project = await Project.findByIdAndDelete(req.params.id);
    if (!project) {
      console.log(`Project with ID ${req.params.id} not found`);
      return res.status(404).send('Project not found');
    }
    console.log(`Deleted project: ${project.naziv}`);
    res.redirect('/projects');
  } catch (err) {
    console.error('Delete error:', err);
    res.status(400).send(err.message);
  }
});

// DODAVANJE ČLANA TIMA NA PROJEKT
router.post('/:id/clan', async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    project.clanoviTima.push(req.body);
    await project.save();
    res.redirect(`/projects/${req.params.id}`);
  } catch (err) {
    res.status(400).send(err);
  }
});

// TOGGLE TASK COMPLETION
router.post('/:id/tasks/:taskId/toggle', async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    const task = project.tasks.id(req.params.taskId);
    task.completed = !task.completed;
    task.completedAt = task.completed ? new Date() : null;
    await project.save();
    res.redirect(`/projects/${req.params.id}`);
  } catch (err) {
    res.status(400).send(err);
  }
});

module.exports = router;