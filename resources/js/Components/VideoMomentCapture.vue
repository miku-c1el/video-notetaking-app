<script setup>
import { router } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import { Trash2 } from 'lucide-vue-next';
import { Card } from '@/Components/card';
import axios from 'axios';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';


const props = defineProps({
  noteId: Number,
  player: Object,
  getCurrentTime: Function,
  moments:Array,
});

const moments = ref(props.moments.map(moment => ({
  ...moment,
  editor: useEditor({
    extensions: [StarterKit],
    content: moment.content,
    editorProps: {
      attributes: {
        class: 'prose prose-sm focus:outline-none min-h-[100px] max-w-none'
      }
    },
    onUpdate: ({ editor }) => {
      handleContentChange({
        ...moment,
        content: editor.getHTML()
      });
    }
  })
})));

const title = ref('');
const content = ref('');

const editor = useEditor({
  extensions: [
    StarterKit,
  ],
  content: '',
  editorProps: {
    attributes: {
      class: 'prose prose-sm focus:outline-none min-h-[100px] max-w-none'
    }
  }
});

const formatTimestamp = (seconds) => {
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = Math.floor(seconds % 60);
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const createMoment = async () => {
  const currentTime = props.getCurrentTime();
  props.player.pauseVideo();

  try {
    const response = await axios.post(route('moments.store'),
      { 
        noteId: props.noteId,
        timestamp: currentTime,
        title: title.value,
        content: content.value
      });

      const newMoment = {
        ...response.data.moment,
        editor: useEditor({
          extensions: [StarterKit],
          content: response.data.moment.content,
          editorProps: {
            attributes: {
              class: 'prose prose-sm focus:outline-none min-h-[100px] max-w-none'
            }
          },
          onUpdate: ({ editor }) => {
            handleContentChange({
              ...response.data.moment,
              content: editor.getHTML()
            });
          }
        })
      };

      moments.value = [...moments.value, newMoment].sort((a,b) => a.timestamp - b.timestamp);
      title.value = '';
      content.value = '';

    } catch (error) {
      console.error('Failed to create moment:', error);
    }
};

const updateMoment = async (moment) => {
  try {
    const response = await axios.put(route('moments.update', moment.id), {
      title: moment.title,
      content: moment.content
    });
    
    // 更新されたモーメントで配列を更新
    const updatedMoment = response.data.moment;
    console.log(updatedMoment);
    const index = moments.value.findIndex(m => m.id === updatedMoment.id);
    if (index !== -1) {
      moments.value[index] = {
        ...updatedMoment,
        editor: moments.value[index].editor
      };
      // エディタのコンテンツを明示的に更新
      moments.value[index].editor?.commands.setContent(updatedMoment.content);
    }
  } catch (error) {
    console.error('Failed to update moment:', error);
  }
};

const deleteMoment = async (momentId) => {
  if (confirm('このモーメントを削除してもよろしいですか？')) {
    try {
      await axios.delete(route('moments.destroy', momentId));
      const momentToDelete = moments.value.find(moment => moment.id === momentId);
      if (momentToDelete?.editor) {
        momentToDelete.editor.destroy();
      }

      // 削除されたモーメントを配列から除去
      moments.value = moments.value.filter(moment => moment.id !== momentId);
    } catch (error) {
      console.error('Failed to delete moment:', error);
    }
  }
};


const copyTimestamp = (timestamp) => {
  navigator.clipboard.writeText(formatTimestamp(timestamp));
};

const jumpToTimestamp = (timestamp) => {
  if (props.player) {
    props.player.seekTo(timestamp);
  }
};

let autoSaveTimeout;
const handleContentChange = (moment) => {
  if (!moment) return;
  
  clearTimeout(autoSaveTimeout);
  autoSaveTimeout = setTimeout(() => {
    updateMoment(moment);
  }, 1000);
};

// クリーンアップ関数
onMounted(() => {
  return () => {
    moments.value.forEach(moment => {
      if (moment.editor) {
        moment.editor.destroy();
      }
    });
  };
});
</script>
<template>
  <div class="h-screen border-l border-gray-200">
    <div class="h-full bg-gray-50 p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">モーメント ({{ moments.length }})</h2>
        <button
          @click="createMoment"
          class="px-4 py-2 bg-coral-500 rounded-md hover:bg-coral-600"
        >
          モーメントをキャプチャ
        </button>
      </div>
      
      <div class="space-y-4 overflow-y-auto h-[calc(100vh-100px)]">
        <Card v-for="moment in moments" :key="moment.id" class="p-4 bg-white">
          <div class="flex justify-between items-start mb-2">
            <div class="flex items-center space-x-2">
              <button
                @click="jumpToTimestamp(moment.timestamp)"
                class="p-1 hover:bg-gray-100 rounded"
              >
                {{ formatTimestamp(moment.timestamp) }}
              </button>
            </div>
            <button
              @click="deleteMoment(moment.id)"
              class="p-1 hover:text-red-500 transition-colors"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
          
          <div class="border-t border-b border-gray-100 py-2">
            <input 
              type="text" 
              class="w-full border-none focus:ring-0 text-lg font-medium"
              placeholder="見出し" 
              v-model="moment.title"
              @input="handleContentChange(moment)"
            >
            <EditorContent
                v-if="moment.editor"
                :editor="moment.editor"
                class="p-2"
            />
            <div class="flex gap-2 mt-2">
              <button 
                @click="moment.editor?.chain().focus().toggleBold().run()"
                :class="{ 'is-active': moment.editor?.isActive('bold') }"
                class="p-1 hover:bg-gray-100 rounded"
              >
                <span class="font-mono">B</span>
              </button>
              <button 
                @click="moment.editor?.chain().focus().toggleItalic().run()"
                :class="{ 'is-active': moment.editor?.isActive('italic') }"
                class="p-1 hover:bg-gray-100 rounded"
              >
                <span class="italic font-mono">I</span>
              </button>
              <button 
                @click="moment.editor?.chain().focus().toggleStrike().run()"
                :class="{ 'is-active': moment.editor?.isActive('strike') }"
                class="p-1 hover:bg-gray-100 rounded"
              >
                <span class="underline font-mono">U</span>
              </button>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>

<style>
.ProseMirror {
  min-height: 100px;
  padding: 0.5rem;
}

.ProseMirror:focus {
  outline: none;
}

.ProseMirror p {
  margin: 0;
}

.is-active {
  background-color: #e5e7eb;
}
</style>



