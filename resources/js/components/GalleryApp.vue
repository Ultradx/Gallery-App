<template>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Filter by Tags</h2>
      <div v-for="tag in tags" :key="tag.id" class="tag-item">
        <label>
          <input type="checkbox" :value="tag.name" v-model="selectedTags" @change="applyFilters" />
          <span :class="{ active: selectedTags.includes(tag.name) }">{{ tag.name }}</span>
        </label>
      </div>
      <button class="btn" @click="clearFilters">Clear Filters</button>
    </aside>

    <!-- Gallery -->
    <main class="gallery">
      <h1>Trading Screenshot Gallery</h1>
      <div v-if="screenshots.length === 0" class="no-screenshots">
        No screenshots found for selected filters.
      </div>
      <div class="grid">
        <div
          v-for="(screenshot, index) in screenshots"
          :key="screenshot.id"
          class="grid-item"
          @click="openLightbox(index)"
        >
          <img :src="`/storage/${screenshot.file_path}`" :alt="screenshot.title" />
          <div class="overlay">{{ screenshot.title }}</div>
        </div>
      </div>
    </main>

    <!-- Lightbox Modal -->
    <transition name="fade">
      <div v-if="selectedIndex !== null" class="lightbox-overlay">
        <button class="lightbox-exit" @click="closeLightbox">✕</button>
        <button class="lightbox-prev" @click="prevImage">‹</button>
        <div class="lightbox-content">
          <img
            :src="`/storage/${screenshots[selectedIndex].file_path}`"
            :alt="screenshots[selectedIndex].title"
            class="lightbox-image"
          />
          <p class="lightbox-title">{{ screenshots[selectedIndex].title }}</p>
        </div>
        <button class="lightbox-next" @click="nextImage">›</button>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';

const screenshots = ref([]);
const tags = ref([]);
const selectedTags = ref([]);
const selectedIndex = ref(null);

const fetchScreenshots = async () => {
  try {
    let url = '/api/screenshots';
    if (selectedTags.value.length > 0) {
      const query = selectedTags.value.join(',');
      url += `?tags=${query}`;
    }
    const res = await axios.get(url);
    screenshots.value = res.data;
  } catch (err) {
    console.error(err);
  }
};

const fetchTags = async () => {
  try {
    const res = await axios.get('/api/tags');
    tags.value = res.data;
  } catch (err) {
    console.error(err);
  }
};

const applyFilters = () => fetchScreenshots();
const clearFilters = () => {
  selectedTags.value = [];
  fetchScreenshots();
};
const openLightbox = (index) => (selectedIndex.value = index);
const closeLightbox = () => (selectedIndex.value = null);

const prevImage = () => {
  selectedIndex.value =
    selectedIndex.value > 0 ? selectedIndex.value - 1 : screenshots.value.length - 1;
};

const nextImage = () => {
  selectedIndex.value =
    selectedIndex.value < screenshots.value.length - 1 ? selectedIndex.value + 1 : 0;
};

const handleKeydown = (event) => {
  if (selectedIndex.value === null) return;
  if (event.key === 'ArrowLeft') prevImage();
  if (event.key === 'ArrowRight') nextImage();
  if (event.key === 'Escape') closeLightbox();
};

onMounted(() => {
  fetchTags();
  fetchScreenshots();
  window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeydown);
});
</script>

<style>
/* General container */
.container {
  display: flex;
  height: 100vh;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f0f0f5;
}

/* Sidebar */
.sidebar {
  width: 250px;
  padding: 20px;
  border-right: 1px solid #ddd;
  background-color: #fff;
  overflow-y: auto;
  box-shadow: 2px 0 5px rgba(0,0,0,0.05);
}

.sidebar h2 {
  margin-bottom: 20px;
  font-size: 18px;
  color: #333;
}

.tag-item {
  margin-bottom: 12px;
}

.tag-item input[type="checkbox"] {
  margin-right: 10px;
  accent-color: #4f46e5;
}

.tag-item span.active {
  font-weight: bold;
  color: #4f46e5;
}

.btn {
  margin-top: 20px;
  padding: 10px 14px;
  border: 1px solid #ccc;
  border-radius: 6px;
  background-color: #eee;
  cursor: pointer;
  transition: all 0.2s;
}

.btn:hover {
  background-color: #ddd;
  transform: scale(1.02);
}

/* Gallery */
.gallery {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
}

.gallery h1 {
  margin-bottom: 20px;
  font-size: 24px;
  color: #222;
}

.no-screenshots {
  color: #888;
  font-style: italic;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 20px;
}

.grid-item {
  position: relative;
  cursor: pointer;
  overflow: hidden;
  border-radius: 10px;
  border: 1px solid #ccc;
  background-color: #fff;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  animation: fadeInItem 0.5s ease forwards;
}

.grid-item img {
  width: 100%;
  height: 120px;
  object-fit: cover;
  transition: transform 0.5s;
}

.grid-item:hover {
  transform: scale(1.03);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.grid-item:hover img {
  transform: scale(1.1);
}

.overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0,0,0,0.6);
  color: white;
  font-size: 14px;
  padding: 5px;
  text-align: center;
  opacity: 0;
  transition: opacity 0.3s;
}

.grid-item:hover .overlay {
  opacity: 1;
}

@keyframes fadeInItem {
  from { opacity: 0; transform: translateY(15px);}
  to { opacity: 1; transform: translateY(0);}
}

/* Lightbox */
.lightbox-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  opacity: 0;
  animation: fadeInOverlay 0.3s forwards;
}

@keyframes fadeInOverlay {
  to { opacity: 1; }
}

.lightbox-content {
  max-width: 85%;
  max-height: 90%;
  text-align: center;
}

.lightbox-image {
  max-width: 100%;
  max-height: 90vh;
  border-radius: 12px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.4);
  transform: scale(1.05);
  transition: transform 0.3s;
}

.lightbox-image:hover {
  transform: scale(1.08);
}

.lightbox-title {
  color: white;
  margin-top: 12px;
  font-size: 20px;
  font-style: italic;
}

/* Lightbox buttons */
.lightbox-exit {
  position: fixed;
  top: 20px;
  right: 20px;
  background: white;
  border: none;
  border-radius: 50%;
  padding: 10px 14px;
  cursor: pointer;
  font-size: 16px;
  transition: transform 0.2s;
}

.lightbox-exit:hover {
  transform: rotate(90deg) scale(1.1);
}

.lightbox-prev,
.lightbox-next {
  position: fixed;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: white;
  font-size: 50px;
  cursor: pointer;
  user-select: none;
  transition: transform 0.2s, color 0.2s;
}

.lightbox-prev:hover,
.lightbox-next:hover {
  transform: translateY(-50%) scale(1.2);
  color: #4f46e5;
}

.lightbox-prev { left: 25px; }
.lightbox-next { right: 25px; }

/* Vue transition */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
