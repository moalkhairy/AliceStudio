/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
*/
import { GoogleGenAI, Modality, Part } from '@google/genai';

// --- DOM Elements ---
const form = document.getElementById('prompt-form') as HTMLFormElement;
const modelInput = document.getElementById('model-input') as HTMLInputElement;
const sceneInput = document.getElementById('scene-input') as HTMLInputElement;
const accessoryTypeInput = document.getElementById('accessory-type-input') as HTMLInputElement;
const fileInput = document.getElementById('accessory-upload') as HTMLInputElement;
const imagePreviewContainer = document.getElementById('image-preview-container') as HTMLDivElement;
const generateBtn = document.getElementById('generate-btn') as HTMLButtonElement;
const resultContainer = document.getElementById('result-container') as HTMLElement;
const spinner = document.querySelector('.spinner') as HTMLDivElement;
const fileDropArea = document.querySelector('.file-drop-area') as HTMLDivElement;
const modelPresetsContainer = document.getElementById('model-presets-container') as HTMLDivElement;
const accessoryPresetsContainer = document.getElementById('accessory-presets') as HTMLDivElement;
const scenePresetsContainer = document.getElementById('scene-presets') as HTMLDivElement;
const clearFormBtn = document.getElementById('clear-form-btn') as HTMLButtonElement;

// New elements for page switching
const nav = document.querySelector('nav') as HTMLElement;
const modelPage = document.getElementById('model-page') as HTMLDivElement;
const placeholderPage = document.getElementById('placeholder-page') as HTMLDivElement;
const placeholderInput = document.getElementById('placeholder-input') as HTMLInputElement;
const placeholderPresets = document.getElementById('placeholder-presets') as HTMLDivElement;

// New elements for gender-specific model styles and poses
const femaleModelStyles = document.getElementById('female-model-styles') as HTMLDivElement;
const maleModelStyles = document.getElementById('male-model-styles') as HTMLDivElement;
const femalePoses = document.getElementById('female-poses') as HTMLDivElement;
const malePoses = document.getElementById('male-poses') as HTMLDivElement;

// Camera elements
const useCameraBtn = document.getElementById('use-camera-btn') as HTMLButtonElement;
const cameraModal = document.getElementById('camera-modal') as HTMLDivElement;
const cameraFeed = document.getElementById('camera-feed') as HTMLVideoElement;
const cameraCanvas = document.getElementById('camera-canvas') as HTMLCanvasElement;
const captureBtn = document.getElementById('capture-btn') as HTMLButtonElement;
const closeCameraBtn = document.getElementById('close-camera-btn') as HTMLButtonElement;
const cameraOverlay = cameraModal.querySelector('.camera-modal-overlay') as HTMLDivElement;

// Gallery elements
const gallerySection = document.getElementById('gallery-section') as HTMLElement;
const galleryContainer = document.getElementById('gallery-container') as HTMLDivElement;
const clearGalleryBtn = document.getElementById('clear-gallery-btn') as HTMLButtonElement;
const emptyGalleryMsg = document.getElementById('empty-gallery-msg') as HTMLParagraphElement;


// --- State ---
let accessoryImages: Array<{ mimeType: string; data: string; file: File }> = [];
const modelAttributes = new Map<string, Set<string>>();
const selectedAccessoryTypes = new Set<string>();
let cameraStream: MediaStream | null = null;
type GalleryImage = { id: number; src: string; };
const GALLERY_STORAGE_key = 'accessoryStylistGallery';

// --- Constants ---
const MAX_IMAGES = 3;
const MAX_ACCESSORY_TYPES = 4;
const GALLERY_LIMIT = 7;
const styleGroupConflicts: { [key: string]: string[] } = {
    elegant: ['casual', 'alternative', 'professional'],
    casual: ['elegant', 'alternative', 'professional'],
    alternative: ['elegant', 'casual', 'professional'],
    professional: ['elegant', 'casual', 'alternative'],
    'male-classic': ['male-modern', 'male-alt', 'male-professional'],
    'male-modern': ['male-classic', 'male-alt', 'male-professional'],
    'male-alt': ['male-classic', 'male-modern', 'male-professional'],
    'male-professional': ['male-classic', 'male-modern', 'male-alt'],
};


// --- API Initialization ---
const ai = new GoogleGenAI({apiKey: process.env.API_KEY});

// --- Utility Functions ---
/**
 * Converts a file to a base64 string.
 */
async function fileToGenerativePart(file: File): Promise<{ mimeType: string; data: string; file: File; }> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => {
      const base64Data = (reader.result as string).split(',')[1];
      if (base64Data) {
        resolve({
          mimeType: file.type,
          data: base64Data,
          file: file,
        });
      } else {
        reject(new Error("Failed to read file as base64."));
      }
    };
    reader.onerror = (error) => reject(error);
    reader.readAsDataURL(file);
  });
}

function setLoading(isLoading: boolean) {
  generateBtn.disabled = isLoading;
  spinner.hidden = !isLoading;
}

function displayError(message: string) {
  resultContainer.innerHTML = `<p class="error">${message}</p>`;
  resultContainer.classList.remove('grid-view');
}

function displayResults(candidates: any[]) {
  resultContainer.innerHTML = '';
  resultContainer.classList.remove('grid-view');

  if (!candidates || candidates.length === 0) {
    displayError("The AI did not return any images. Please try again.");
    return;
  }

  const imageParts = candidates.map(candidate =>
    candidate.content?.parts.find((part: Part) => part.inlineData)
  ).filter(Boolean);

  if (imageParts.length === 0) {
    displayError("The AI response did not contain any images. Please try a different prompt.");
    return;
  }

  if (imageParts.length > 1) {
    resultContainer.classList.add('grid-view');
  }

  const isGalleryFull = getGalleryImages().length >= GALLERY_LIMIT;

  for (const part of imageParts) {
    if (part.inlineData) {
      const wrapper = document.createElement('div');
      wrapper.classList.add('result-image-wrapper');

      const img = document.createElement('img');
      img.src = `data:${part.inlineData.mimeType};base64,${part.inlineData.data}`;
      img.alt = 'Generated image of a model wearing an accessory';
      
      const actionsWrapper = document.createElement('div');
      actionsWrapper.classList.add('result-actions');

      const downloadBtn = document.createElement('button');
      downloadBtn.type = 'button';
      downloadBtn.classList.add('result-action-btn');
      downloadBtn.setAttribute('aria-label', 'Download image');
      downloadBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>`;

      downloadBtn.addEventListener('click', () => {
        const link = document.createElement('a');
        link.href = img.src;
        link.download = `generated-image-${Date.now()}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      });

      const saveBtn = document.createElement('button');
      saveBtn.type = 'button';
      saveBtn.classList.add('result-action-btn');
      saveBtn.setAttribute('aria-label', 'Save image to gallery');
      saveBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>`;
      
      saveBtn.addEventListener('click', () => {
          saveImageToGallery(img.src);
          saveBtn.classList.add('is-saved');
          saveBtn.disabled = true;
          saveBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
      });

      if (isGalleryFull) {
          saveBtn.disabled = true;
          saveBtn.setAttribute('aria-label', 'Save to gallery (full)');
      }

      actionsWrapper.appendChild(saveBtn);
      actionsWrapper.appendChild(downloadBtn);

      wrapper.appendChild(img);
      wrapper.appendChild(actionsWrapper);
      resultContainer.appendChild(wrapper);
    }
  }
}

function renderImagePreviews() {
    imagePreviewContainer.innerHTML = ''; // Clear existing previews
    accessoryImages.forEach((imagePart, index) => {
        const reader = new FileReader();
        reader.onload = () => {
            const previewItem = document.createElement('div');
            previewItem.classList.add('preview-item');
            previewItem.innerHTML = `
                <img src="${reader.result as string}" alt="Accessory preview ${index + 1}">
                <button type="button" class="remove-image-btn" aria-label="Remove image ${index + 1}" data-index="${index}">&times;</button>
            `;
            imagePreviewContainer.appendChild(previewItem);
        };
        reader.readAsDataURL(imagePart.file);
    });

    const fileMsg = fileDropArea.querySelector('.file-msg');
    if (fileMsg) {
        if (accessoryImages.length > 0) {
            fileMsg.textContent = `${accessoryImages.length} image(s) selected`;
        } else {
            fileMsg.textContent = 'Drag & drop or click to upload (up to 3)';
        }
    }

    if (accessoryImages.length > 0) {
        fileDropArea.classList.add('is-active');
    } else {
        fileDropArea.classList.remove('is-active');
    }
}


function removeImage(index: number) {
    if (index >= 0 && index < accessoryImages.length) {
        accessoryImages.splice(index, 1);
        fileInput.value = ''; // Reset file input
        renderImagePreviews();
    }
}

function clearAllUploadedImages() {
    accessoryImages = [];
    fileInput.value = '';
    renderImagePreviews();
}


function clearForm() {
    // Stop any active camera stream
    closeCamera();

    // Reset basic form fields to their initial state in HTML
    form.reset();

    // Clear JS-managed states and UI that form.reset() doesn't handle
    // 1. Clear model description input and underlying data
    modelInput.value = '';
    modelAttributes.clear();
    modelPresetsContainer.querySelectorAll('.preset-btn-model.is-active').forEach(btn => {
        (btn as HTMLButtonElement).classList.remove('is-active');
    });

    // 2. Clear accessory types
    selectedAccessoryTypes.clear();
    accessoryPresetsContainer.querySelectorAll('.preset-btn-model.is-active').forEach(btn => {
        btn.classList.remove('is-active');
    });
    // Re-enable all accessory buttons
    accessoryPresetsContainer.querySelectorAll('button').forEach(btn => {
        btn.disabled = false;
    });

    // Reset style and pose visibility to default (female)
    femaleModelStyles.hidden = false;
    maleModelStyles.hidden = true;
    femalePoses.hidden = false;
    malePoses.hidden = true;

    // After clearing attributes, update presets that depend on them
    updateStylePresetStates(); // Reset style disabled states
    updateContextualPresets();

    // 3. Remove the uploaded image preview and clear the state
    clearAllUploadedImages();

    // 4. Clear any previous results
    resultContainer.innerHTML = '';
    resultContainer.classList.remove('grid-view');

    // 5. Reset nav to default (Model Studio)
    document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('is-active'));
    document.getElementById('nav-model-btn')?.classList.add('is-active');

    modelPage.style.display = 'flex';
    placeholderPage.style.display = 'none';
    modelInput.required = true;
    sceneInput.required = true;
    placeholderInput.required = false;

    // 6. Give focus to the first interactive element for better UX
    modelInput.focus();
}


// --- Gallery Functions ---

function getGalleryImages(): GalleryImage[] {
    const storedImages = localStorage.getItem(GALLERY_STORAGE_key);
    return storedImages ? JSON.parse(storedImages) : [];
}

function saveGalleryImages(images: GalleryImage[]) {
    localStorage.setItem(GALLERY_STORAGE_key, JSON.stringify(images));
}

function updateGalleryVisibility() {
    const images = getGalleryImages();
    const hasImages = images.length > 0;
    emptyGalleryMsg.hidden = hasImages;
    clearGalleryBtn.hidden = !hasImages;
}

function updateSaveButtonsState() {
    const isGalleryFull = getGalleryImages().length >= GALLERY_LIMIT;
    // Select only buttons that haven't been successfully used yet
    const saveButtons = resultContainer.querySelectorAll('.result-action-btn:not(.is-saved)');
    saveButtons.forEach(button => {
        (button as HTMLButtonElement).disabled = isGalleryFull;
        if (isGalleryFull) {
            button.setAttribute('aria-label', 'Save to gallery (full)');
        } else {
            button.setAttribute('aria-label', 'Save image to gallery');
        }
    });
}

function renderGalleryItem(image: GalleryImage) {
    const item = document.createElement('div');
    item.classList.add('gallery-item');
    item.dataset.id = image.id.toString();

    const img = document.createElement('img');
    img.src = image.src;
    img.alt = 'Saved generated accessory image';

    const actionsWrapper = document.createElement('div');
    actionsWrapper.classList.add('gallery-item-actions');

    const downloadBtn = document.createElement('button');
    downloadBtn.type = 'button';
    downloadBtn.classList.add('gallery-action-btn');
    downloadBtn.setAttribute('aria-label', 'Download image');
    downloadBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>`;

    downloadBtn.addEventListener('click', () => {
        const link = document.createElement('a');
        link.href = img.src;
        link.download = `gallery-image-${image.id}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    const deleteBtn = document.createElement('button');
    deleteBtn.type = 'button';
    deleteBtn.classList.add('gallery-action-btn');
    deleteBtn.setAttribute('aria-label', 'Delete saved image');
    deleteBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;

    deleteBtn.addEventListener('click', () => {
        deleteImageFromGallery(image.id);
    });

    actionsWrapper.appendChild(downloadBtn);
    actionsWrapper.appendChild(deleteBtn);

    item.appendChild(img);
    item.appendChild(actionsWrapper);
    galleryContainer.appendChild(item);
}

function loadGallery() {
    galleryContainer.innerHTML = '';
    const images = getGalleryImages();
    images.forEach(renderGalleryItem);
    updateGalleryVisibility();
}

function saveImageToGallery(imageSrc: string) {
    const images = getGalleryImages();
    if (images.length >= GALLERY_LIMIT) {
        alert(`Your gallery is full. Please delete an image before saving a new one.`);
        return;
    }
    const newImage: GalleryImage = {
        id: Date.now(),
        src: imageSrc,
    };
    images.push(newImage);
    saveGalleryImages(images);
    renderGalleryItem(newImage);
    updateGalleryVisibility();
    updateSaveButtonsState();
}

function deleteImageFromGallery(id: number) {
    let images = getGalleryImages();
    images = images.filter(img => img.id !== id);
    saveGalleryImages(images);

    const itemToRemove = galleryContainer.querySelector(`.gallery-item[data-id="${id}"]`);
    if (itemToRemove) {
        itemToRemove.remove();
    }
    updateGalleryVisibility();
    updateSaveButtonsState();
}

function clearGallery() {
    if (confirm('Are you sure you want to delete all saved images? This action cannot be undone.')) {
        localStorage.removeItem(GALLERY_STORAGE_key);
        galleryContainer.innerHTML = '';
        updateGalleryVisibility();
        updateSaveButtonsState();
    }
}

/**
 * Builds a natural language description of the model based on selected attributes.
 * This description is shown in the input field and used in the prompt.
 */
function buildModelDescription(attributes: Map<string, Set<string>>): string {
    const gender = Array.from(attributes.get('gender') || [])[0] || '';
    const age = Array.from(attributes.get('age') || [])[0];
    const ethnicity = Array.from(attributes.get('ethnicity') || [])[0];

    // Part 1: Core identity (e.g., "a young adult East Asian woman")
    let coreIdentity = [age, ethnicity, gender].filter(Boolean).join(' ');

    if (!coreIdentity) {
        coreIdentity = 'a model';
    } else if (!gender && (age || ethnicity)) {
        coreIdentity += ' model';
    }

    // Part 2: "with" clauses for hair and appearance
    const withClauses: string[] = [];
    const hairStyle = (Array.from(attributes.get('hair_style') || [])[0] || '').replace(' hair', '');
    const hairColor = (Array.from(attributes.get('hair_color') || [])[0] || '').replace(' hair', '');
    const hairDescription = `${hairStyle} ${hairColor} hair`.trim();

    if (hairDescription !== 'hair') {
        withClauses.push(hairDescription);
    }
    
    const appearance = Array.from(attributes.get('appearance') || []);
    // The preset already has "with ", so we remove it to avoid "with with glasses"
    appearance.forEach(app => withClauses.push(app.replace(/^with /i, '')));

    let withPart = '';
    if (withClauses.length > 0) {
        // Creates a nice list like "long blonde hair, glasses, and tattoos"
        // Fix: Cast Intl to any to resolve TypeScript error for ListFormat.
        const formattedClauses = new (Intl as any).ListFormat('en', { style: 'long', type: 'conjunction' }).format(withClauses.filter(c => c));
        withPart = ` with ${formattedClauses}`;
    }

    // Part 3: Pose description
    const pose = Array.from(attributes.get('pose') || []);
    let posePart = '';
    if (pose.length > 0) {
        // Fix: Cast Intl to any to resolve TypeScript error for ListFormat.
        const formattedPoses = new (Intl as any).ListFormat('en', { style: 'long', type: 'conjunction' }).format(pose);
        posePart = `, posing in a ${formattedPoses} manner`;
    }

    return `${coreIdentity}${withPart}${posePart}`;
}


/**
 * Disables conflicting model style presets based on current selections.
 * e.g., If "Glamour" (elegant group) is chosen, "Casual" presets are disabled.
 */
function updateStylePresetStates() {
    const visibleContainer = !maleModelStyles.hidden ? maleModelStyles : femaleModelStyles;
    
    const allStyleButtons = visibleContainer.querySelectorAll('button[data-category="style"]') as NodeListOf<HTMLButtonElement>;
    const activeStyleButtons = visibleContainer.querySelectorAll<HTMLButtonElement>('button[data-category="style"].is-active');

    const activeGroups = new Set<string>();
    activeStyleButtons.forEach(btn => {
        if (btn.dataset.styleGroup) {
            activeGroups.add(btn.dataset.styleGroup);
        }
    });

    const conflictingGroups = new Set<string>();
    activeGroups.forEach(group => {
        styleGroupConflicts[group]?.forEach(conflict => conflictingGroups.add(conflict));
    });

    allStyleButtons.forEach(btn => {
        const btnGroup = btn.dataset.styleGroup;
        const isActive = btn.classList.contains('is-active');

        if (btnGroup && conflictingGroups.has(btnGroup) && !isActive) {
            btn.disabled = true;
        } else {
            btn.disabled = false;
        }
    });
}


/**
 * Updates scene presets based on the selected model style for a more logical UX.
 * e.g., Disables "indoor" scenes if "Beach Vibe" style is selected.
 */
function updateContextualPresets() {
    const styles = modelAttributes.get('style') || new Set();
    const scenePresetsButtons = scenePresetsContainer.querySelectorAll('.preset-btn-model') as NodeListOf<HTMLButtonElement>;
    const incompatibleScenes = ['urban', 'studio', 'indoor', 'night'];
    const beachVibeScene = 'on a sunny beach with clear blue water';

    if (styles.has('Beach Vibe')) {
        sceneInput.value = beachVibeScene;

        scenePresetsButtons.forEach(btn => {
            btn.classList.remove('is-active'); // Deactivate active scene presets
            const btnText = btn.textContent?.toLowerCase() || '';
            if (incompatibleScenes.includes(btnText)) {
                btn.disabled = true;
            } else {
                btn.disabled = false;
            }
        });
    } else {
        scenePresetsButtons.forEach(btn => {
            btn.disabled = false;
        });
        if (sceneInput.value === beachVibeScene) {
            sceneInput.value = '';
        }
    }
}


// --- Event Handlers ---
async function handleFileSelect(files: FileList | null) {
    if (!files) return;

    if (accessoryImages.length + files.length > MAX_IMAGES) {
        displayError(`You can only upload a maximum of ${MAX_IMAGES} images.`);
        return;
    }

    try {
        for (const file of Array.from(files)) {
            const imagePart = await fileToGenerativePart(file);
            accessoryImages.push(imagePart);
        }
        renderImagePreviews();
    } catch (error) {
        console.error("Error reading file(s):", error);
        displayError("Could not process one or more uploaded files. Please try again.");
    }
}

fileInput.addEventListener('change', (e) => {
  const target = e.target as HTMLInputElement;
  handleFileSelect(target.files);
});

imagePreviewContainer.addEventListener('click', (e) => {
    const target = e.target as HTMLElement;
    if (target.classList.contains('remove-image-btn')) {
        const index = parseInt(target.dataset.index || '-1');
        removeImage(index);
    }
});


form.addEventListener('submit', async (e) => {
  e.preventDefault();
  if (accessoryImages.length === 0) {
    displayError("Please upload an accessory image first.");
    return;
  }

  setLoading(true);
  resultContainer.innerHTML = '<p>AI is generating your image...</p>';
  resultContainer.classList.remove('grid-view');

  const accessoryType = accessoryTypeInput.value || 'accessory';
  let textPrompt = '';
  const isModelMode = modelPage.style.display !== 'none';
  const aspectRatio = (document.querySelector('input[name="aspect-ratio"]:checked') as HTMLInputElement)?.value || '1:1';

  // --- Grammar helpers for dynamic prompts ---
  const isPluralAccessory = selectedAccessoryTypes.size > 1;
  const accessoryNoun = isPluralAccessory ? 'accessories' : accessoryType;
  const accessoryVerb = isPluralAccessory ? 'are' : 'is';
  const imageNoun = accessoryImages.length > 1 ? 'images' : 'image';


  let aspectRatioText = '';
    switch (aspectRatio) {
        case '16:9':
            aspectRatioText = `The final output image must be rendered in a widescreen landscape format with a 16:9 aspect ratio, ignoring the aspect ratio of the uploaded ${imageNoun}.`;
            break;
        case '9:16':
            aspectRatioText = `The final output image must be rendered in a tall portrait format with a 9:16 aspect ratio, ignoring the aspect ratio of the uploaded ${imageNoun}.`;
            break;
        case '1:1':
        default:
            aspectRatioText = `The final output image must be rendered in a square format with a 1:1 aspect ratio, ignoring the aspect ratio of the uploaded ${imageNoun}.`;
            break;
    }

  if (isModelMode) {
    const model = modelInput.value;
    const scene = sceneInput.value;
    const proportion = (document.querySelector('input[name="proportion"]:checked') as HTMLInputElement)?.value || 'natural';
    const lighting = (document.querySelector('input[name="lighting"]:checked') as HTMLInputElement)?.value || 'automatic';
    const style = Array.from(modelAttributes.get('style') || []);
    
    if (!model || !scene) {
        displayError("Please provide a model and scene description.");
        setLoading(false);
        return;
    }

    let proportionText = '';
    switch (proportion) {
        case 'small':
            proportionText = `Crucially, the ${accessoryNoun} must be rendered as very small and delicate on the model. It should be a subtle accent, not a dominant feature, appearing much smaller than its typical size.`;
            break;
        case 'large':
            proportionText = `Slightly increase the size of the ${accessoryNoun} to make it a prominent focal point. It should be noticeably larger than average, creating a bold statement, but must remain believable and aesthetically pleasing, avoiding unrealistic exaggeration.`;
            break;
        case 'natural':
        default:
            proportionText = `Render the ${accessoryNoun} in a natural, realistic, and true-to-life proportion, perfectly scaled to the model's features as it would be worn in reality. Ensure the size is coherent and believable.`;
            break;
    }
    
    let lightingText = '';
    switch (lighting) {
        case 'soft':
            lightingText = 'The lighting should be soft, natural, and diffused, like from an overcast sky or a large window, creating gentle shadows and a flattering, realistic look.';
            break;
        case 'dramatic':
            lightingText = 'Employ dramatic studio lighting with high contrast, deep shadows, and sharp highlights (chiaroscuro) to create a moody, high-fashion, and impactful image.';
            break;
        case 'golden':
            lightingText = 'The scene must be illuminated by the warm, soft, and directional light of the \'golden hour\' (just after sunrise or before sunset), casting long shadows and creating a dreamy, evocative, and warm-toned atmosphere.';
            break;
        case 'cinematic':
            lightingText = 'The lighting should be cinematic and dramatic, using strong key lights and fill lights to create a moody, film-like atmosphere with rich colors and deep, narrative-driven shadows.';
            break;
        case 'backlit':
            lightingText = 'The main light source should be placed behind the model, creating a bright outline (halo effect) around their silhouette and separating them from the background. This should create a sense of depth and romance.';
            break;
        case 'rim':
            lightingText = 'Utilize strong rim lighting from the side or back to trace the contours of the model and accessory with a thin, bright line of light, emphasizing their shape and creating a dramatic separation from the background.';
            break;
        case 'automatic':
        default:
            lightingText = 'The lighting should be professional and flattering, consistent with a high-end advertising campaign.';
            break;
    }

    const styleText = style.length > 0 ? `${new (Intl as any).ListFormat('en').format(style)} style, ` : '';

    textPrompt = `A ${styleText}professional, high-resolution, and photorealistic photograph of ${model}, prominently featuring the ${accessoryType} from the provided ${imageNoun}. ` +
                 `The primary goal is to create a stunning product shot where the accessory is the undeniable focal point. ` +
                 `It is critical that the generated ${accessoryNoun} ${accessoryVerb} an absolutely identical, flawless replica of the one(s) in the upload. Replicate every detail with extreme precision: color, texture, shape, materials, and any unique features like gemstones or clasps. Pay special attention to replicating intricate textures (like fabric weaves, leather grain, or metallic finishes), small embellishments (such as tiny jewels, engravings, or stitching), and specific material properties (like glossiness, matte finishes, or transparency). Do not alter, simplify, or reinterpret the accessory in any way. ` +
                 `The accessory must be physically and realistically integrated with the model, appearing as if it is truly being worn. Pay meticulous attention to contact points, shadows, and interactions with skin, hair, and clothing. For instance, a necklace should drape naturally following the contours of the neck and collarbones, a hat must sit correctly on the head and interact with the hair, and fabrics like scarves must show realistic folds and draping. All attachment points, such as clasps or earring posts, must be rendered believably. ` +
                 `The model's pose and the overall composition must be expertly crafted to showcase the ${accessoryNoun}, drawing the viewer's eye directly to it. The model should complement the accessory, not compete with it. ` +
                 `The scene is ${scene}. ${proportionText} ${lightingText} ` +
                 `While the image must be hyper-realistic, especially the model's skin texture (which should be natural and detailed, not waxy or artificial), the accessory must remain the hero of the image. ` +
                 `Ensure realistic shadows, reflections, and perspective that enhance the accessory's appearance. ${aspectRatioText}`;

  } else { // Placeholder mode
    const placeholderSetup = placeholderInput.value;
     if (!placeholderSetup) {
        displayError("Please provide a photoshoot setup description.");
        setLoading(false);
        return;
    }
    textPrompt = `A professional, high-resolution, and photorealistic product photograph of the ${accessoryType} from the provided ${imageNoun}. It is critical that the generated ${accessoryNoun} ${accessoryVerb} an absolutely identical, flawless replica of the one(s) in the upload. Replicate every detail with extreme precision: color, texture, shape, materials, and any unique features like gemstones or clasps. Pay special attention to replicating intricate textures (like fabric weaves, leather grain, or metallic finishes), small embellishments (such as tiny jewels, engravings, or stitching), and specific material properties (like glossiness, matte finishes, or transparency). Do not alter, simplify, or reinterpret the accessory in any way. ` +
                 `The ${accessoryNoun} ${isPluralAccessory ? 'are' : 'is'} displayed in the following setup: ${placeholderSetup}. ` +
                 `The lighting should be clean and professional, suitable for an e-commerce website or a magazine advertisement. ` +
                 `The final image must be completely realistic, with perfect integration of the ${accessoryNoun} into the scene, including accurate shadows and reflections. ${aspectRatioText}`
  }


  const imageParts: Part[] = accessoryImages.map(img => ({ inlineData: { mimeType: img.mimeType, data: img.data }}));
  const parts: Part[] = [
    ...imageParts,
    { text: textPrompt }
  ];

  try {
    const response = await ai.models.generateContent({
      model: 'gemini-2.5-flash-image-preview',
      contents: { parts: parts },
      config: {
        responseModalities: [Modality.IMAGE, Modality.TEXT],
      },
    });

    if (response.candidates && response.candidates.length > 0) {
        displayResults(response.candidates);
    } else {
        displayError("The AI did not return a valid response. Please try again.");
    }

  } catch (error) {
    console.error("API Error:", error);
    displayError("An error occurred while generating the image. Please check your setup and try again.");
  } finally {
    setLoading(false);
  }
});

// Handle model preset button clicks
modelPresetsContainer.addEventListener('click', (e) => {
    const target = e.target as HTMLButtonElement;
    if (target.tagName !== 'BUTTON' || !target.dataset.category) return;

    const category = target.dataset.category;
    const value = target.textContent || '';
    const allowMultiple = target.dataset.allowMultiple === 'true';

    if (!modelAttributes.has(category)) {
        modelAttributes.set(category, new Set<string>());
    }

    const categorySet = modelAttributes.get(category)!;
    const parent = target.parentElement;

    if (allowMultiple) {
        if (categorySet.has(value)) {
            categorySet.delete(value);
            target.classList.remove('is-active');
        } else {
            categorySet.add(value);
            target.classList.add('is-active');
        }
    } else { // Single-select logic
        const isActive = target.classList.contains('is-active');
        parent?.querySelectorAll('button').forEach(btn => btn.classList.remove('is-active'));
        categorySet.clear();

        if (!isActive) {
            categorySet.add(value);
            target.classList.add('is-active');
        }

        if (category === 'gender') {
            // Clear all style attributes and UI state when gender changes
            modelAttributes.get('style')?.clear();
            document.querySelectorAll('#female-model-styles .is-active, #male-model-styles .is-active').forEach(btn => {
                btn.classList.remove('is-active');
            });

            // Clear pose attributes and UI state when gender changes
            modelAttributes.get('pose')?.clear();
            document.querySelectorAll('#female-poses .is-active, #male-poses .is-active').forEach(btn => {
                btn.classList.remove('is-active');
            });
            
            const selectedGender = Array.from(categorySet)[0];
            const isMan = selectedGender === 'Male Model';
            maleModelStyles.hidden = !isMan;
            femaleModelStyles.hidden = isMan;
            malePoses.hidden = !isMan;
            femalePoses.hidden = isMan;
        }
    }
    
    modelInput.value = buildModelDescription(modelAttributes);

    if (category === 'style' || category === 'gender') {
        updateStylePresetStates();
    }
    updateContextualPresets();
});


// Handle scene preset button clicks
scenePresetsContainer.addEventListener('click', (e) => {
    const target = e.target as HTMLButtonElement;
    // Check if it's a button and not disabled
    if (target.tagName !== 'BUTTON' || !target.classList.contains('preset-btn-model') || target.disabled) {
        return;
    }

    const group = target.dataset.sceneGroup;
    const isActive = target.classList.contains('is-active');

    // Handle single-select groups
    if (group) {
        // If we are about to activate it, deactivate others in the same group.
        if (!isActive) {
            scenePresetsContainer.querySelectorAll<HTMLButtonElement>(`.preset-btn-model[data-scene-group="${group}"]`).forEach(btn => {
                btn.classList.remove('is-active');
            });
        }
    }

    // Toggle the current button's active state
    target.classList.toggle('is-active');

    // Rebuild the input value from all active buttons
    const activeButtons = scenePresetsContainer.querySelectorAll<HTMLButtonElement>('.preset-btn-model.is-active');
    const sceneParts = Array.from(activeButtons).map(btn => btn.textContent?.trim()).filter(Boolean);
    sceneInput.value = sceneParts.join(' ');
});

// Handle placeholder preset clicks
placeholderPresets.addEventListener('click', (e) => {
    const target = e.target as HTMLButtonElement;
    if (target.classList.contains('preset-btn-model')) {
        placeholderInput.value = target.dataset.prompt || target.textContent || '';
    }
});


// Handle accessory preset button clicks
accessoryPresetsContainer.addEventListener('click', (e) => {
    const target = e.target as HTMLButtonElement;
    if (target.tagName !== 'BUTTON') return;

    const value = target.textContent?.trim() || '';
    
    if (selectedAccessoryTypes.has(value)) {
        selectedAccessoryTypes.delete(value);
        target.classList.remove('is-active');
    } else {
        if (selectedAccessoryTypes.size >= MAX_ACCESSORY_TYPES) {
            return; 
        }
        selectedAccessoryTypes.add(value);
        target.classList.add('is-active');
    }

    // Update the disabled state of all buttons
    const allAccessoryButtons = accessoryPresetsContainer.querySelectorAll('button');
    if (selectedAccessoryTypes.size >= MAX_ACCESSORY_TYPES) {
        allAccessoryButtons.forEach(btn => {
            if (!selectedAccessoryTypes.has(btn.textContent?.trim() || '')) {
                btn.disabled = true;
            }
        });
    } else {
        allAccessoryButtons.forEach(btn => {
            btn.disabled = false;
        });
    }

    const accessoryList = Array.from(selectedAccessoryTypes);
    const formattedText = new (Intl as any).ListFormat('en').format(accessoryList);
    accessoryTypeInput.value = formattedText;
});


// Handle page switching via nav
nav.addEventListener('click', (e) => {
    const target = e.target as HTMLButtonElement;
    if (!target.dataset.page) return;

    const pageId = target.dataset.page;
    
    document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('is-active'));
    target.classList.add('is-active');

    const isModelPage = pageId === 'model-page';
    modelPage.style.display = isModelPage ? 'flex' : 'none';
    placeholderPage.style.display = isModelPage ? 'none' : 'flex';


    // Update required attributes for form validation
    modelInput.required = isModelPage;
    sceneInput.required = isModelPage;
    placeholderInput.required = !isModelPage;
});


// --- Drag and Drop ---
function preventDefaults(e: Event) {
  e.preventDefault();
  e.stopPropagation();
}

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
  fileDropArea.addEventListener(eventName, preventDefaults, false);
});

['dragenter', 'dragover'].forEach(eventName => {
  fileDropArea.addEventListener(eventName, () => fileDropArea.classList.add('is-active'), false);
});

['dragleave', 'drop'].forEach(eventName => {
  fileDropArea.addEventListener(eventName, () => fileDropArea.classList.remove('is-active'), false);
});

fileDropArea.addEventListener('drop', (e) => {
  const dt = (e as DragEvent).dataTransfer;
  const files = dt?.files;
  if (files) {
    fileInput.files = files; // Assign dropped files to the input
    handleFileSelect(files);
  }
}, false);

// --- Camera Functions ---
async function openCamera() {
    if (accessoryImages.length >= MAX_IMAGES) {
        alert(`You can only upload a maximum of ${MAX_IMAGES} images.`);
        return;
    }
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        cameraFeed.srcObject = cameraStream;
        cameraModal.hidden = false;
        document.body.style.overflow = 'hidden'; // Prevent scrolling while modal is open
    } catch (err) {
        console.error("Camera access denied:", err);
        alert("Could not access the camera. Please ensure you have a camera connected and have granted permission in your browser.");
    }
}

function closeCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
    }
    cameraFeed.srcObject = null;
    cameraModal.hidden = true;
    document.body.style.overflow = '';
}

function captureImage() {
    if (!cameraStream) return;
    
    const videoWidth = cameraFeed.videoWidth;
    const videoHeight = cameraFeed.videoHeight;
    
    cameraCanvas.width = videoWidth;
    cameraCanvas.height = videoHeight;
    
    const context = cameraCanvas.getContext('2d');
    if (context) {
        // Flip the canvas to match the mirrored video feed
        context.translate(videoWidth, 0);
        context.scale(-1, 1);
        context.drawImage(cameraFeed, 0, 0, videoWidth, videoHeight);
    }

    cameraCanvas.toBlob(async (blob) => {
        if (blob) {
            const file = new File([blob], `capture-${Date.now()}.png`, { type: 'image/png' });
            const fileList = new DataTransfer();
            fileList.items.add(file);
            await handleFileSelect(fileList.files);
        }
        closeCamera();
    }, 'image/png');
}

// Initialize form state
document.addEventListener('DOMContentLoaded', () => {
    // Set initial page visibility
    modelPage.style.display = 'flex';
    placeholderPage.style.display = 'none';

    modelInput.required = true;
    sceneInput.required = true;
    placeholderInput.required = false;

    // Gallery initialization
    loadGallery();
    clearGalleryBtn.addEventListener('click', clearGallery);
    clearFormBtn.addEventListener('click', clearForm);
    
    // Camera event listeners
    useCameraBtn.addEventListener('click', openCamera);
    closeCameraBtn.addEventListener('click', closeCamera);
    cameraOverlay.addEventListener('click', closeCamera);
    captureBtn.addEventListener('click', captureImage);
});