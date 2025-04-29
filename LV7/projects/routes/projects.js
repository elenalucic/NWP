const express = require('express');
const router = express.Router();
const Project = require('../models/Project');
const User = require('../models/User');

// Middleware za provjeru autentifikacije
const ensureAuthenticated = (req, res, next) => {
  if (req.isAuthenticated()) {
    return next();
  }
  res.redirect('/auth/login');
};

// FORMA ZA KREIRANJE NOVOG PROJEKTA
router.get('/new', ensureAuthenticated, async (req, res) => {
  const users = await User.find();
  res.render('projects/new', { users });
});

// CREATE
router.post('/', ensureAuthenticated, async (req, res) => {
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
    const clanoviTima = req.body.clanoviTima ? (Array.isArray(req.body.clanoviTima) ? req.body.clanoviTima : [req.body.clanoviTima]) : [];
    await Project.create({
      ...req.body,
      tasks,
      voditelj: req.user._id,
      clanoviTima
    });
    res.redirect('/projects');
  } catch (err) {
    console.error(err);
    res.status(400).send(err.message);
  }
});

// READ ALL - POPIS PROJEKATA
router.get('/', ensureAuthenticated, async (req, res) => {
  try {
    const role = req.query.role || 'all'; // Default: prikaz svih projekata
    let query = { arhiviran: false };

    if (role === 'leader') {
      query.voditelj = req.user._id;
    } else if (role === 'member') {
      query.clanoviTima = req.user._id;
    }

    const projects = await Project.find(query)
      .populate('voditelj')
      .populate('clanoviTima');
    res.render('projects/index', { projects, role });
  } catch (err) {
    console.error(err);
    res.status(400).send(err.message);
  }
});

// ARHIVA PROJEKATA
router.get('/archive', ensureAuthenticated, async (req, res) => {
  const projects = await Project.find({
    $or: [
      { voditelj: req.user._id },
      { clanoviTima: req.user._id }
    ],
    arhiviran: true
  })
    .populate('voditelj')
    .populate('clanoviTima');
  res.render('projects/archive', { projects });
});

// READ ONE - PRIKAZ JEDNOG PROJEKTA
router.get('/:id', ensureAuthenticated, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id)
      .populate('voditelj')
      .populate('clanoviTima');
    if (!project) {
      return res.status(404).send('Projekt nije pronađen');
    }
    const users = await User.find();
    res.render('projects/show', { project, users });
  } catch (err) {
    console.error(err);
    res.status(400).send(err.message);
  }
});

// FORMA ZA UREĐIVANJE
router.get('/:id/edit', ensureAuthenticated, async (req, res) => {
  const project = await Project.findById(req.params.id)
    .populate('voditelj')
    .populate('clanoviTima');
  if (!project.voditelj.equals(req.user._id)) {
    return res.status(403).send('Samo voditelj može uređivati projekt.');
  }
  const users = await User.find();
  res.render('projects/edit', { project, users });
});

// SPREMANJE IZMJENA
router.put('/:id', ensureAuthenticated, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    if (!project.voditelj.equals(req.user._id)) {
      return res.status(403).send('Samo voditelj može uređivati projekt.');
    }
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
    const clanoviTima = req.body.clanoviTima ? (Array.isArray(req.body.clanoviTima) ? req.body.clanoviTima : [req.body.clanoviTima]) : [];
    const updateData = {
      naziv: req.body.naziv,
      opis: req.body.opis,
      cijena: req.body.cijena ? Number(req.body.cijena) : undefined,
      datumPocetka: req.body.datumPocetka ? new Date(req.body.datumPocetka) : undefined,
      datumZavrsetka: req.body.datumZavrsetka ? new Date(req.body.datumZavrsetka) : undefined,
      arhiviran: req.body.arhiviran === 'on',
      tasks,
      clanoviTima
    };
    await Project.findByIdAndUpdate(req.params.id, updateData, {
      new: true,
      runValidators: true
    });
    res.redirect('/projects');
  } catch (err) {
    console.error('Update error:', err);
    res.status(400).send(err.message);
  }
});

// BRISANJE PROJEKTA
router.delete('/:id', ensureAuthenticated, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    if (!project.voditelj.equals(req.user._id)) {
      return res.status(403).send('Samo voditelj može brisati projekt.');
    }
    await Project.findByIdAndDelete(req.params.id);
    res.redirect('/projects');
  } catch (err) {
    console.error('Delete error:', err);
    res.status(400).send(err.message);
  }
});

// DODAVANJE ČLANA TIMA NA PROJEKT
router.post('/:id/clan', ensureAuthenticated, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    if (!project.voditelj.equals(req.user._id)) {
      return res.status(403).send('Samo voditelj može dodavati članove.');
    }
    const userId = req.body.userId;
    if (!project.clanoviTima.includes(userId)) {
      project.clanoviTima.push(userId);
      await project.save();
    }
    res.redirect(`/projects/${req.params.id}`);
  } catch (err) {
    res.status(400).send(err);
  }
});

// TOGGLE TASK COMPLETION
router.post('/:id/tasks/:taskId/toggle', ensureAuthenticated, async (req, res) => {
  try {
    const project = await Project.findById(req.params.id);
    if (!project.voditelj.equals(req.user._id) && !project.clanoviTima.includes(req.user._id)) {
      return res.status(403).send('Samo voditelj ili član tima može mijenjati zadatke.');
    }
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