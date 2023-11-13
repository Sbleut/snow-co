const addvideoFormDeleteLink = (item) => {
    const removeFormButton = document.createElement('button');
    removeFormButton.classList.add('btn', 'btn-danger', 'm-1');
    removeFormButton.innerText = 'Delete this video';

    item.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the video form
        item.remove();
    });
}

const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');
    item.classList.add('list-group-item', 'm-1');

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;

    // add a delete link to the new form
    addvideoFormDeleteLink(item);
};

const collectionHelper = () => {
    document
        .querySelectorAll('.add_item_link')
        .forEach(btn => {
            btn.addEventListener("click", addFormToCollection)
        });
    document
        .querySelectorAll('ul.videos li')
        .forEach((video) => {
            addvideoFormDeleteLink(video)
        });
}

document.addEventListener('DOMContentLoaded', () => {
    collectionHelper();
});